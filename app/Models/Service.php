<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'estimated_duration_minutes',
        'allow_home_service',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'allow_home_service' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class);
    }
}
