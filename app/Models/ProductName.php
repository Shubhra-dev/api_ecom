<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductName extends Model
{
    use HasFactory;

    protected $appends = [
        'code',
    ];

    public function getCodeAttribute() {
        $product_id = (string) ($this->product_id ?? "");

        return str_pad($product_id, 6, "0", STR_PAD_LEFT);
    }
}
