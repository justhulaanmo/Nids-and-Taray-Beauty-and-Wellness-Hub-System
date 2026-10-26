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
        $user = Auth::user();

        // Only Admin can view all employee commissions and overall tracker
        if (!$user || $user->role !== 'admin') {
            return redirect()->route('wallet.index')->with('info', 'Employees can view their personal earnings in My Wallet.');
        }

        $selectedDate = $request->input('date', Carbon::today()->format('Y-m-d'));
        $targetDate = Carbon::parse($selectedDate);
        $isStaffUser = false;
        $currentStaffProfile = null;

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
        $user = Auth::user();
        $staff = StaffProfile::findOrFail($id);

        // Non-admins can only view their own profile breakdown
        if ($user && $user->role !== 'admin') {
            if (!$user->staffProfile || $user->staffProfile->id != $id) {
                return redirect()->route('wallet.index')->with('info', 'Employees can view their personal earnings in My Wallet.');
            }
        }

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

    public function updateEmployeeRole(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'admin') {
            abort(403, 'Only Administrators can update employee roles.');
        }

        $staff = StaffProfile::findOrFail($id);

        $validated = $request->validate([
            'specialization' => 'required|in:manicurist,massage_therapist,all_around',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'nickname' => 'required|string|max:50',
            'role' => 'required|in:staff,admin',
            'is_active' => 'required|boolean',
        ]);

        $staff->specialization = $validated['specialization'];
        $staff->commission_rate = $validated['commission_rate'];
        $staff->nickname = $validated['nickname'];
        $staff->is_active = $validated['is_active'];
        $staff->save();

        if ($staff->user) {
            $staff->user->role = $validated['role'];
            $staff->user->is_active = $validated['is_active'];
            $staff->user->save();
        }

        return back()->with('success', "Updated {$staff->nickname}'s role to " . ucwords(str_replace('_', ' ', $staff->specialization)) . " successfully.");
    }
}
