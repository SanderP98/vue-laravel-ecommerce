<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderDetails extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id', 'product_id', 'quantity', 'total_price'
    ];

    protected $table = 'order_details';

    public function product() {
        return $this->belongsTo(Product::class, 'product_id')->withTrashed();
    }

    public function order() {
        return $this->belongsTo(Order::class, 'order_id')->withTrashed();
    }
}
