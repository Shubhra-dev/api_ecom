<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EcommerceOrder extends Model
{
    use HasFactory;

    protected $table = 'ecommerce_orders';

    public function items()
    {
        return $this->hasMany(EcommerceOrderItem::class, 'order_id');
    }

    public function payment()
    {
        return $this->hasOne(EcommercePayment::class, 'order_id');
    }
}
