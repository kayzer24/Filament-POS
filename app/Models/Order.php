<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'customer_id',
        'total_price',
        'date',
        'discount',
        'discount_amount',
        'total_payment',
        'status',
        'payment_method',
        'payment_status',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    protected static function booted()
    {
        static::updated(function ($order) {
            $originalStatus = $order->getOriginal('status');

            if ($order->isDirty('status') && $order->status === 'completed') {
                foreach ($order->orderDetails as $detail) {
                    $product = $detail->product;

                    if ($product) {
                        $product->decrement('stock', $detail->quantity);
                    }
                }
            }

            if ($order->isDirty('status') && $originalStatus === 'completed' && $order->status === 'cancelled') {
                foreach ($order->orderDetails as $detail) {
                    $product = $detail->product;

                    if ($product) {
                        $product->increment('stock', $detail->quantity);
                    }
                }
            }
        });
    }
}
