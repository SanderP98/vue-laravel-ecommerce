<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'country', 'address', 'address_2', 'city', 'state', 'postal_code'
    ];

    public function order() {
        return $this->belongsTo(Order::class, 'address_id');
    }
}
