<?php

namespace App\Http\Controllers;

use App\Models\Payout;
use App\Models\StaffProfile;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PayoutController extends Controller
{
    /**
     * Admin Shop Net Profit Cashout
     */
    public function storeAdminPayout(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'admin') {
            abort(403, 'Only administrators can request shop profit cashouts.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:100',
            'payout_method' => 'required|in:gcash,maya,bank',
            'account_name' => 'required|string|max:100',
            'account_number' => ['required', 'string', 'regex:/^09\d{9}$/'],
            'bank_name' => 'nullable|required_if:payout_method,bank|string|max:100',
        ], [
            'account_number.regex' => 'Ang Mobile / Account number ay dapat 11 digits at nagsisimula sa 09 (Halimbawa: 09171234567).',
        ]);

        // Calculate available net profit (deduct commissions, gateway fees, and operating expenses)
        $totalGross = (float) Transaction::where('payment_status', 'paid')->where('status', '!=', 'cancelled')->sum('total_amount');
        $totalQR = (float) Transaction::where('payment_status', 'paid')->where('status', '!=', 'cancelled')->whereIn('payment_method', ['paymongo_qr', 'paymongo_link'])->sum('total_amount');
        $paymongoFees = round($totalQR * 0.025, 2);
        $totalCommissions = (float) TransactionItem::whereHas('transaction', fn($q) => $q->where('payment_status', 'paid')->where('status', '!=', 'cancelled'))->sum('staff_commission_amount');
        $totalExpenses = (float) \App\Models\Expense::sum('amount');
        $netShopProfit = max(0, $totalGross - $totalCommissions - $paymongoFees - $totalExpenses);
        
        $alreadyCashedOut = (float) Payout::where('type', 'admin_shop')->where('status', 'completed')->sum('gross_amount');
        $availableForCashout = max(0, $netShopProfit - $alreadyCashedOut);

        $requestedAmount = (float) $request->amount;

        if ($requestedAmount > $availableForCashout) {
            return back()->withErrors([
                'amount' => 'Requested cashout amount (₱' . number_format($requestedAmount, 2) . ') exceeds available net shop profit (₱' . number_format($availableForCashout, 2) . ').'
            ])->withInput();
        }

        // Payout transfer fee: ₱0 for GCash/Maya test, ₱15 for bank InstaPay
        $transferFee = ($request->payout_method === 'bank') ? 15.00 : 0.00;
        $netTransfer = max(0, $requestedAmount - $transferFee);

        $referenceNo = Payout::generateReferenceNo();
        $poutSimId = 'pout_sim_' . uniqid();

        Payout::create([
            'reference_no' => $referenceNo,
            'user_id' => $user->id,
            'staff_id' => null,
            'type' => 'admin_shop',
            'gross_amount' => $requestedAmount,
            'fee' => $transferFee,
            'net_amount' => $netTransfer,
            'payout_method' => $request->payout_method,
            'account_name' => ucwords(strtolower(trim($request->account_name))),
            'account_number' => $request->account_number,
            'bank_name' => $request->bank_name,
            'status' => 'completed',
            'paymongo_payout_id' => $poutSimId,
            'notes' => 'PayMongo Test Mode Simulated Payout. Dispatched to ' . strtoupper($request->payout_method),
            'completed_at' => now(),
        ]);

        return back()
            ->with('success', "Cashout request #{$referenceNo} of ₱" . number_format($netTransfer, 2) . " to {$request->account_name} was processed successfully via PayMongo!")
            ->with('payout_receipt', [
                'reference_no' => $referenceNo,
                'amount' => $netTransfer,
                'payout_method' => strtoupper($request->payout_method) . ($request->bank_name ? " ({$request->bank_name})" : ''),
                'account_name' => ucwords(strtolower(trim($request->account_name))),
                'account_number' => $request->account_number,
                'paymongo_payout_id' => $poutSimId,
                'completed_at' => now()->format('M d, Y • h:i A'),
            ]);
    }

    /**
     * Staff Commission Cashout
     */
    public function storeStaffPayout(Request $request)
    {
        $user = Auth::user();
        $staffProfile = StaffProfile::where('user_id', $user->id)->first();

        if (!$staffProfile) {
            return back()->withErrors(['payout' => 'No employee profile linked to this account.']);
        }

        $request->validate([
            'amount' => 'required|numeric|min:50',
            'payout_method' => 'required|in:gcash,maya,bank,cash_counter',
            'account_name' => 'required|string|max:100',
            'account_number' => ['required', 'string', 'regex:/^09\d{9}$/'],
        ], [
            'account_number.regex' => 'Ang Mobile / Account number ay dapat 11 digits at nagsisimula sa 09 (Halimbawa: 09171234567).',
        ]);

        // Total earned paid commissions using Net formula: (Gross - PayMongo Fee if QR) * Commission Rate
        $allStaffItems = TransactionItem::where('staff_id', $staffProfile->id)
            ->whereHas('transaction', fn($q) => $q->where('payment_status', 'paid')->where('status', '!=', 'cancelled'))
            ->with('transaction')
            ->get();

        $earnedCommissions = (float) $allStaffItems->sum(function ($item) use ($staffProfile) {
            $isPayMongo = $item->transaction && in_array($item->transaction->payment_method, ['paymongo_qr', 'paymongo_link']);
            $paymongoFee = $isPayMongo ? round((float) $item->price * 0.025, 2) : 0.0;
            $netBase = max(0, (float) $item->price - $paymongoFee);
            $commRate = (float) ($item->commission_rate ?: $staffProfile->commission_rate);
            return round(($netBase * $commRate) / 100, 2);
        });

        $alreadyWithdrawn = (float) Payout::where('staff_id', $staffProfile->id)
            ->where('status', 'completed')
            ->sum('gross_amount');

        $pendingPayouts = (float) Payout::where('staff_id', $staffProfile->id)
            ->where('status', 'pending')
            ->sum('gross_amount');

        $availableBalance = max(0, round($earnedCommissions - $alreadyWithdrawn - $pendingPayouts, 2));
        $requestedAmount = (float) $request->amount;

        if ($requestedAmount > $availableBalance) {
            return back()->withErrors([
                'amount' => 'Requested cashout (₱' . number_format($requestedAmount, 2) . ') exceeds your available commission balance (₱' . number_format($availableBalance, 2) . ').'
            ])->withInput();
        }

        $referenceNo = Payout::generateReferenceNo();
        $poutSimId = 'pout_sim_' . uniqid();

        Payout::create([
            'reference_no' => $referenceNo,
            'user_id' => $user->id,
            'staff_id' => $staffProfile->id,
            'type' => 'staff_commission',
            'gross_amount' => $requestedAmount,
            'fee' => 0.00,
            'net_amount' => $requestedAmount,
            'payout_method' => $request->payout_method,
            'account_name' => ucwords(strtolower(trim($request->account_name))),
            'account_number' => $request->account_number,
            'status' => 'completed',
            'paymongo_payout_id' => $poutSimId,
            'notes' => 'Staff Commission Cashout via ' . strtoupper($request->payout_method),
            'completed_at' => now(),
        ]);

        return back()
            ->with('success', "Commission payout #{$referenceNo} of ₱" . number_format($requestedAmount, 2) . " processed successfully via PayMongo Disbursements!")
            ->with('payout_receipt', [
                'reference_no' => $referenceNo,
                'amount' => $requestedAmount,
                'payout_method' => strtoupper($request->payout_method),
                'account_name' => ucwords(strtolower(trim($request->account_name))),
                'account_number' => $request->account_number,
                'paymongo_payout_id' => $poutSimId,
                'completed_at' => now()->format('M d, Y • h:i A'),
            ]);
    }

    /**
     * Void a test payout to restore staff balance during testing
     */
    public function voidPayout(int|string $id)
    {
        $user = Auth::user();
        $payout = Payout::findOrFail($id);

        if ($user->role !== 'admin' && $payout->user_id !== $user->id) {
            abort(403, 'Unauthorized to void this payout.');
        }

        $amount = (float) $payout->gross_amount;
        $ref = $payout->reference_no;
        $payout->delete();

        return back()->with('success', "Test payout #{$ref} was voided. ₱" . number_format($amount, 2) . " has been restored to your available balance for testing!");
    }
}
