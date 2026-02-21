<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Uom extends Model
{
    protected $fillable = [
        'code',
        'name',
        'base_unit_id',
        'symbol',
        'description',
        'is_active',
    ];

    public function baseUnit(): BelongsTo
    {
        return $this->belongsTo(BaseUnit::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
