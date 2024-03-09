<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    use HasFactory;

    protected $fillable = ['url'];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function prices()
    {
        return $this->hasMany(AdPrice::class);
    }

    // У вашій моделі Ad
    public function getLatestPriceAttribute()
    {
        return $this->prices()->latest('created_at')->first() ?? null; // Повертає останню ціну або null, якщо цін немає
    }


}
