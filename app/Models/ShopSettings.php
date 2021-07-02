<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'image', 'email', 'tel_nr', 'mollie_api_key'
    ];
}
