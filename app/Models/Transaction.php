<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'tracking_number',
        'customer_name',
        'customer_phone',
        'service_type',
        'home_service_address',
        'home_service_fee',
        'subtotal',
        'discount',
        'total_amount',
        'payment_method',
        'payment_status',
        'payment_reference',
        'paymongo_checkout_id',
        'paymongo_checkout_url',
        'status',
        'notes',
        'satisfaction_rating',
        'created_by',
        'completed_at',
    ];

    protected $casts = [
        'home_service_fee' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function generateTrackingNumber()
    {
        $date = now()->format('Ymd');
        $count = self::whereDate('created_at', today())->count() + 1;
        return 'NT-' . $date . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }
}
