<?php

namespace App\Http\Controllers;

use App\Models\StaffProfile;
use Illuminate\Http\Request;

class QueueController extends Controller
{
    public function index()
    {
        $allStaff = StaffProfile::where('is_active', true)
            ->with('user')
            ->orderBy('queue_position')
            ->get();

        // Single batch aggregation query for today's served counts
        $todayServedMap = \App\Models\TransactionItem::whereHas('transaction', function ($q) {
                $q->whereDate('created_at', \Carbon\Carbon::today())->where('status', '!=', 'cancelled');
            })
            ->select('staff_id', \Illuminate\Support\Facades\DB::raw('count(distinct transaction_id) as total'))
            ->groupBy('staff_id')
            ->pluck('total', 'staff_id');

        foreach ($allStaff as $staff) {
            $staff->today_served_count = $todayServedMap[$staff->id] ?? 0;
        }

        $availableQueue = $allStaff->where('status', 'available')->sortBy('queue_position');
        $servingInShop = $allStaff->where('status', 'serving_in_shop');
        $onHomeService = $allStaff->where('status', 'on_home_service');
        $onBreak = $allStaff->where('status', 'on_break');
        $offDuty = $allStaff->where('status', 'off_duty');

        return view('queue.index', compact(
            'availableQueue',
            'servingInShop',
            'onHomeService',
            'onBreak',
            'offDuty',
            'allStaff'
        ));
    }

    public function updateStatus(Request $request, $id)
    {
        $staff = StaffProfile::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:available,serving_in_shop,on_home_service,on_break,off_duty',
        ]);

        $newStatus = $request->input('status');
        
        // If moving back to available, put them at the end of the available queue for fairness
        if ($newStatus === 'available' && $staff->status !== 'available') {
            $maxQueue = StaffProfile::where('status', 'available')->max('queue_position') ?? 0;
            $staff->queue_position = $maxQueue + 1;
        }

        $staff->status = $newStatus;
        $staff->last_status_change = now();
        $staff->save();

        return back()->with('success', "Status updated for {$staff->nickname} ({$staff->name}): " . $staff->status_badge['label']);
    }

    public function moveQueue(Request $request, $id, $direction)
    {
        $staff = StaffProfile::findOrFail($id);
        
        if ($direction === 'up' && $staff->queue_position > 1) {
            $prevStaff = StaffProfile::where('status', 'available')
                ->where('queue_position', '<', $staff->queue_position)
                ->orderBy('queue_position', 'desc')
                ->first();
                
            if ($prevStaff) {
                $temp = $staff->queue_position;
                $staff->queue_position = $prevStaff->queue_position;
                $prevStaff->queue_position = $temp;
                $staff->save();
                $prevStaff->save();
            }
        } elseif ($direction === 'down') {
            $nextStaff = StaffProfile::where('status', 'available')
                ->where('queue_position', '>', $staff->queue_position)
                ->orderBy('queue_position', 'asc')
                ->first();
                
            if ($nextStaff) {
                $temp = $staff->queue_position;
                $staff->queue_position = $nextStaff->queue_position;
                $nextStaff->queue_position = $temp;
                $staff->save();
                $nextStaff->save();
            }
        }

        return back()->with('success', 'Queue position updated for ' . $staff->nickname . '.');
    }
}
