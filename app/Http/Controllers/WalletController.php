<?php

namespace App\Http\Controllers;

use App\Models\StaffProfile;
use App\Models\TransactionItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $selectedDate = $request->input('date', Carbon::today()->format('Y-m-d'));
        $targetDate = Carbon::parse($selectedDate);

        // Find staff profile for this user
        $staffProfile = StaffProfile::where('user_id', $user->id)->first();

        // If admin doesn't have a staff profile yet, pick or let admin view their own or all
        if (!$staffProfile && $user->role === 'admin') {
            // Check if there's a staff profile with admin's name or pick first
            $staffProfile = StaffProfile::where('name', 'like', '%Nida%')
                ->orWhere('name', 'like', '%Admin%')
                ->first() ?? StaffProfile::first();
        }

        if (!$staffProfile) {
            return redirect()->route('dashboard')->with('info', 'No staff profile is linked to your account.');
        }

        // Get items serviced by this staff on selected date
        $items = TransactionItem::where('staff_id', $staffProfile->id)
            ->whereHas('transaction', function ($q) use ($targetDate) {
                $q->whereDate('created_at', $targetDate)->where('status', '!=', 'cancelled');
            })
            ->with(['service', 'transaction'])
            ->latest()
            ->get();

        $totalCustomersCount = $items->pluck('transaction_id')->unique()->count();
        $totalServicesCount = $items->count();

        // Calculations
        $totalSalesHandled = $items->sum('price');
        $myTotalCommission = $items->where('transaction.payment_status', 'paid')->sum('staff_commission_amount');

        // Cash vs QR breakdown
        $cashItems = $items->filter(fn($i) => $i->transaction && $i->transaction->payment_method === 'cash');
        $qrItems = $items->filter(fn($i) => $i->transaction && $i->transaction->payment_method === 'paymongo_qr');

        $cashCollected = $cashItems->where('transaction.payment_status', 'paid')->sum('price');
        $myCashCommission = $cashItems->where('transaction.payment_status', 'paid')->sum('staff_commission_amount');
        
        // Shop share from cash collected = Cash Collected minus My Commission on Cash
        $cashToRemitToShop = $cashCollected - $myCashCommission;

        // If there are QR payments, the shop received the money directly, so shop owes staff their QR commission
        $qrCommissionOwedToStaff = $qrItems->where('transaction.payment_status', 'paid')->sum('staff_commission_amount');

        // All-Time Commission Balance & Cashouts
        $totalEarnedAllTime = (float) TransactionItem::where('staff_id', $staffProfile->id)
            ->whereHas('transaction', fn($q) => $q->where('payment_status', 'paid')->where('status', '!=', 'cancelled'))
            ->sum('staff_commission_amount');
        $totalStaffWithdrawn = (float) \App\Models\Payout::where('staff_id', $staffProfile->id)->where('status', 'completed')->sum('gross_amount');
        $availableCommissionBalance = max(0, $totalEarnedAllTime - $totalStaffWithdrawn);
        $staffPayouts = \App\Models\Payout::where('staff_id', $staffProfile->id)->latest()->take(5)->get();

        return view('wallet.index', compact(
            'staffProfile',
            'items',
            'selectedDate',
            'totalCustomersCount',
            'totalServicesCount',
            'totalSalesHandled',
            'myTotalCommission',
            'cashCollected',
            'cashToRemitToShop',
            'qrCommissionOwedToStaff',
            'totalEarnedAllTime',
            'totalStaffWithdrawn',
            'availableCommissionBalance',
            'staffPayouts'
        ));
    }
}
