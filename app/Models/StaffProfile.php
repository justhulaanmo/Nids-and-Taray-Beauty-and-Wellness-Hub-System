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
     * Get count of customers served today (In-memory cached to eliminate N+1 queries)
     */
    public function getCustomersServedTodayAttribute()
    {
        if (isset($this->today_served_count)) {
            return $this->today_served_count;
        }
        if (isset($this->attributes['today_served_count'])) {
            return $this->attributes['today_served_count'];
        }
        if ($this->relationLoaded('transactionItems')) {
            $today = Carbon::today()->format('Y-m-d');
            return $this->transactionItems
                ->filter(function ($item) use ($today) {
                    return $item->transaction && 
                           substr($item->transaction->created_at, 0, 10) === $today &&
                           $item->transaction->status !== 'cancelled';
                })
                ->pluck('transaction_id')
                ->unique()
                ->count();
        }
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

    /**
     * Check if this staff member can perform a specific service based on specialization.
     */
    public function canPerformService($service): bool
    {
        if ($this->specialization === 'all_around') {
            return true;
        }

        $category = $service->category ?? null;
        $catSlug = strtolower($category->slug ?? '');
        $catName = strtolower($category->name ?? '');
        $srvName = strtolower($service->name ?? '');

        $isNailService = str_contains($catSlug, 'nail') || 
                         str_contains($catName, 'nail') || 
                         str_contains($catName, 'cutic') || 
                         str_contains($srvName, 'manicure') || 
                         str_contains($srvName, 'pedicure') || 
                         str_contains($srvName, 'nail');

        $isMassageService = str_contains($catSlug, 'mass') || 
                            str_contains($catName, 'mass') || 
                            str_contains($catName, 'wellness') || 
                            str_contains($srvName, 'massage') || 
                            str_contains($srvName, 'reflexology') || 
                            str_contains($srvName, 'ventosa');

        if ($this->specialization === 'manicurist') {
            return $isNailService && !$isMassageService;
        }

        if ($this->specialization === 'massage_therapist') {
            return $isMassageService && !$isNailService;
        }

        return true;
    }

    /**
     * Check if this staff member is qualified for a service category.
     */
    public function isQualifiedForCategory($category): bool
    {
        if ($this->specialization === 'all_around') {
            return true;
        }

        $slug = strtolower($category->slug ?? '');
        $name = strtolower($category->name ?? '');

        if ($this->specialization === 'manicurist') {
            return str_contains($slug, 'nail') || str_contains($name, 'nail') || str_contains($name, 'cutic');
        }

        if ($this->specialization === 'massage_therapist') {
            return str_contains($slug, 'mass') || str_contains($name, 'mass') || str_contains($name, 'wellness');
        }

        return false;
    }
}
