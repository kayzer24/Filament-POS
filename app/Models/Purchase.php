<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purchase extends Model
{
    protected $fillable = [
        'purchase_number',
        'user_id',
        'supplier_id',
        'purchase_date',
        'received_date',
        'tax_rate',
        'tax_amount',
        'discount',
        'discount_amount',
        'total_payment',
        'status',
        'status_payment',
        'payment_method',
        'total_before_tax',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseDetails(): HasMany
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    protected static function booted()
    {
        static::created(function($purchase) {
            if ($purchase->status === 'received') {
                foreach ($purchase->purchaseDetails as $detail) {
                    $product = $detail->product;

                    if ($product) {
                        $product->increment('stock', $detail->total_quantity);
                    }
                }
            }
        });

        static::updated(function ($purchase) {
            $originalStatus = $purchase->getOriginal('status');

            if ($purchase->isDirty('status') && $purchase->status === 'received') {

                foreach ($purchase->purchaseDetails as $detail) {
                    $product = $detail->product;

                    if ($product) {
                        $product->increment('stock', $detail->total_quantity);
                    }
                }
            }

            if ($purchase->isDirty('status') && $originalStatus === 'received' && $purchase->status === 'cancelled') {
                foreach ($purchase->purchaseDetails as $detail) {
                    $product = $detail->product;

                    if ($product) {
                        $product->decrement('stock', $detail->total_quantity);
                    }
                }
            }
        });
    }
}
