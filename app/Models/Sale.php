<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use SoftDeletes;

    const STATUS_PENDING   = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id',
        'product_id',
        'customer_name',
        'customer_contact',
        'quantity',
        'unit_price',
        'total_price',
        'cost_price_snapshot',
        'status',
        'notes',
        'image_url',
        'sold_at',
    ];

    protected $casts = [
        'unit_price'          => 'decimal:2',
        'total_price'         => 'decimal:2',
        'cost_price_snapshot' => 'decimal:4',
        'sold_at'             => 'date',
        'quantity'            => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function profitAmount(): float
    {
        return round(($this->unit_price - $this->cost_price_snapshot) * $this->quantity, 2);
    }
}
