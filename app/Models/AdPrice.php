<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdPrice extends Model
{
    use HasFactory;

    protected $fillable = ['ad_id', 'title', 'display_value', 'value', 'currency_code', 'currency_symbol'];

    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }

    

}
