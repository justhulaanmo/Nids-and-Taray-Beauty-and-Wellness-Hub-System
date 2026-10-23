<?php

namespace App\Http\Controllers;

use App\Models\StaffProfile;
use App\Models\TransactionItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffTrackerController extends Controller
{
    public function index(Request $request)
    {
        $selectedDate = $request->input('date', Carbon::today()->format('Y-m-d'));
        $targetDate = Carbon::parse($selectedDate);

        // If logged in as staff and not admin, focus on their own profile
        $user = Auth::user();
        $isStaffUser = $user && $user->role === 'staff';
        $currentStaffProfile = $isStaffUser ? StaffProfile::where('user_id', $user->id)->first() : null;

        $staffList = StaffProfile::where('is_active', true)
            ->with(['transactionItems' => function ($q) use ($targetDate) {
                $q->whereHas('transaction', function ($sub) use ($targetDate) {
                    $sub->whereDate('created_at', $targetDate)->where('status', '!=', 'cancelled');
                })->with(['service', 'transaction']);
            }])
            ->get()
            ->map(function ($staff) {
                $uniqueCustomers = $staff->transactionItems->pluck('transaction_id')->unique()->count();
                $totalServices = $staff->transactionItems->count();
                $totalCommission = $staff->transactionItems->where('transaction.payment_status', 'paid')->sum('staff_commission_amount');
                $pendingCommission = $staff->transactionItems->where('transaction.payment_status', 'pending')->sum('staff_commission_amount');

                $staff->customers_count = $uniqueCustomers;
                $staff->services_count = $totalServices;
                $staff->earned_commission = $totalCommission;
                $staff->pending_commission = $pendingCommission;
                return $staff;
            });

        return view('staff-tracker.index', compact('staffList', 'selectedDate', 'isStaffUser', 'currentStaffProfile'));
    }

    public function showDetails($id, Request $request)
    {
        $staff = StaffProfile::findOrFail($id);
        $selectedDate = $request->input('date', Carbon::today()->format('Y-m-d'));
        $targetDate = Carbon::parse($selectedDate);

        $items = TransactionItem::where('staff_id', $id)
            ->whereHas('transaction', function ($q) use ($targetDate) {
                $q->whereDate('created_at', $targetDate)->where('status', '!=', 'cancelled');
            })
            ->with(['service', 'transaction'])
            ->latest()
            ->get();

        return view('staff-tracker.show', compact('staff', 'items', 'selectedDate'));
    }
}
