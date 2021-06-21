<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'price', 'units', 'description', 'image'
    ];

    public function order_details() {
        return $this->hasMany(Order::class);
    }

    public function product_rating() {
        return $this->hasMany(ProductRating::class);
    }

    public function product_category() {
        return $this->hasOne(ProductCategory::class, 'id', 'category_id');
    }
}
