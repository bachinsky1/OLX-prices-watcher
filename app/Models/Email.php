<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    use HasFactory;

    protected $fillable = ['email', 'confirmed', 'confirmation_token'];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
