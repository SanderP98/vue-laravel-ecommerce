<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasEvents;

class Order extends Model
{
    use HasFactory, SoftDeletes, Notifiable, HasEvents;

    protected $fillable = [
        'user_id', 'address_id', 'total_price', 'order_status', 'is_delivered'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function product() {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function address() {
        return $this->belongsTo(Address::class, 'address_id');
    }
    public function order_details() {
        return $this->hasMany(OrderDetails::class, 'order_id', 'id')->withTrashed();
    }
}
