<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductRating extends Model
{
    use HasFactory;

    protected $table = 'product_ratings';
    
    protected $fillable = [
        'product_id', 'user_id', 'rating', 'title', 'description', 'is_approved'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }
    public function product_image() {
        return $this->hasMany(ProductImage::class, 'product_id', 'product_id');
    }
}
