<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_no',
        'user_id',
        'staff_id',
        'type',
        'gross_amount',
        'fee',
        'net_amount',
        'payout_method',
        'account_name',
        'account_number',
        'bank_name',
        'status',
        'paymongo_payout_id',
        'notes',
        'completed_at',
    ];

    protected $casts = [
        'gross_amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function staff()
    {
        return $this->belongsTo(StaffProfile::class, 'staff_id');
    }

    public static function generateReferenceNo(): string
    {
        $dateStr = now()->format('Ymd');
        $count = self::whereDate('created_at', now()->toDateString())->count() + 1;
        return sprintf('CASHOUT-%s-%03d', $dateStr, $count);
    }
}
