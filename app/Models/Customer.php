<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address_line1',
        'address_line2',
        'postcode',
        'city',
        'country',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
