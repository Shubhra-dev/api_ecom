<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EcommerceOrderItem extends Model
{
    use HasFactory;

    protected $table = 'ecommerce_order_items';

    public function order()
    {
        return $this->belongsTo(EcommerceOrder::class, 'order_id');
    }
}
