<?php

// app/Models/Cart.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $primaryKey = 'cart_id';

    protected $guarded = [];

    public function items()
    {
        return $this->hasMany(CartItem::class, 'cart_id');
    }
}
