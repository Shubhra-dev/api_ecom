<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EcommercePayment extends Model
{
    use HasFactory;

    protected $table = 'ecommerce_payments';

    public function order()
    {
        return $this->belongsTo(EcommerceOrder::class, 'order_id');
    }
}
