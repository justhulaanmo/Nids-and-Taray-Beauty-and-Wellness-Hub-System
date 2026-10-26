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
            'account_number' => 'required|string|max:50',
            'bank_name' => 'nullable|required_if:payout_method,bank|string|max:100',
        ]);

        // Calculate available net profit
        $totalGross = (float) Transaction::where('payment_status', 'paid')->where('status', '!=', 'cancelled')->sum('total_amount');
        $totalQR = (float) Transaction::where('payment_status', 'paid')->where('status', '!=', 'cancelled')->whereIn('payment_method', ['paymongo_qr', 'paymongo_link'])->sum('total_amount');
        $paymongoFees = round($totalQR * 0.025, 2);
        $totalCommissions = (float) TransactionItem::whereHas('transaction', fn($q) => $q->where('payment_status', 'paid')->where('status', '!=', 'cancelled'))->sum('staff_commission_amount');
        $netShopProfit = $totalGross - $totalCommissions - $paymongoFees;
        
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

        Payout::create([
            'reference_no' => $referenceNo,
            'user_id' => $user->id,
            'staff_id' => null,
            'type' => 'admin_shop',
            'gross_amount' => $requestedAmount,
            'fee' => $transferFee,
            'net_amount' => $netTransfer,
            'payout_method' => $request->payout_method,
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
            'bank_name' => $request->bank_name,
            'status' => 'completed',
            'paymongo_payout_id' => 'pout_sim_' . uniqid(),
            'notes' => 'PayMongo Test Mode Simulated Payout. Dispatched to ' . strtoupper($request->payout_method),
            'completed_at' => now(),
        ]);

        return back()->with('success', "Cashout request #{$referenceNo} of ₱" . number_format($netTransfer, 2) . " to {$request->account_name} ({$request->account_number}) was processed successfully via PayMongo!");
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
            'account_number' => 'required|string|max:50',
        ]);

        // Total earned paid commissions
        $earnedCommissions = (float) TransactionItem::where('staff_id', $staffProfile->id)
            ->whereHas('transaction', fn($q) => $q->where('payment_status', 'paid')->where('status', '!=', 'cancelled'))
            ->sum('staff_commission_amount');

        $alreadyWithdrawn = (float) Payout::where('staff_id', $staffProfile->id)
            ->where('status', 'completed')
            ->sum('gross_amount');

        $availableBalance = max(0, $earnedCommissions - $alreadyWithdrawn);
        $requestedAmount = (float) $request->amount;

        if ($requestedAmount > $availableBalance) {
            return back()->withErrors([
                'amount' => 'Requested cashout (₱' . number_format($requestedAmount, 2) . ') exceeds your available commission balance (₱' . number_format($availableBalance, 2) . ').'
            ])->withInput();
        }

        $referenceNo = Payout::generateReferenceNo();

        Payout::create([
            'reference_no' => $referenceNo,
            'user_id' => $user->id,
            'staff_id' => $staffProfile->id,
            'type' => 'staff_commission',
            'gross_amount' => $requestedAmount,
            'fee' => 0.00,
            'net_amount' => $requestedAmount,
            'payout_method' => $request->payout_method,
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
            'status' => 'completed',
            'paymongo_payout_id' => 'pout_sim_' . uniqid(),
            'notes' => 'Staff Commission Cashout via ' . strtoupper($request->payout_method),
            'completed_at' => now(),
        ]);

        return back()->with('success', "Commission payout #{$referenceNo} of ₱" . number_format($requestedAmount, 2) . " processed successfully!");
    }
}
