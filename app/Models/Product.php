<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
       'category_id', 'name', 'price', 'units', 'description'
    ];

    public function order_details() {
        return $this->hasMany(Order::class)->withTrashed();
    }

    public function product_rating() {
        return $this->hasMany(ProductRating::class);
    }

    public function product_category() {
        return $this->hasOne(ProductCategory::class, 'id', 'category_id');
    }

    public function product_image() {
        return $this->hasMany(ProductImage::class);
    }
}
