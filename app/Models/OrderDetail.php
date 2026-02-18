<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDetail extends Model
{
    protected $fillable = [
        'product_id',
        'order_id',
        'subtotal',
        'quantity',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    protected static function booted()
    {
        static::created(function ($orderDetails) {
            if ($orderDetails->order->status === 'completed') {
                $product = $orderDetails->product;

                if ($product) {
                    $product->decrement('stock', $orderDetails->quantity);
                }
            }
        });
    }
}
