<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class StaffProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'nickname',
        'phone',
        'specialization',
        'status',
        'queue_position',
        'commission_rate',
        'is_active',
        'last_status_change',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'commission_rate' => 'decimal:2',
        'last_status_change' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class, 'staff_id');
    }

    /**
     * Get count of customers served today
     */
    public function getCustomersServedTodayAttribute()
    {
        return $this->transactionItems()
            ->whereHas('transaction', function ($q) {
                $q->whereDate('created_at', Carbon::today())
                  ->where('status', '!=', 'cancelled');
            })
            ->distinct('transaction_id')
            ->count('transaction_id');
    }

    /**
     * Get total commission earned today
     */
    public function getCommissionEarnedTodayAttribute()
    {
        return $this->transactionItems()
            ->whereHas('transaction', function ($q) {
                $q->whereDate('created_at', Carbon::today())
                  ->where('payment_status', 'paid');
            })
            ->sum('staff_commission_amount');
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'available' => ['label' => 'Available', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
            'serving_in_shop' => ['label' => 'Serving In-Shop', 'class' => 'bg-teal-50 text-teal-700 border-teal-200'],
            'on_home_service' => ['label' => 'On Home Service', 'class' => 'bg-sky-50 text-sky-700 border-sky-200'],
            'on_break' => ['label' => 'On Break', 'class' => 'bg-amber-50 text-amber-700 border-amber-200'],
            'off_duty' => ['label' => 'Off Duty', 'class' => 'bg-stone-100 text-stone-600 border-stone-200'],
            default => ['label' => $this->status, 'class' => 'bg-stone-100 text-stone-600 border-stone-200'],
        };
    }
}
