<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HomeProduct extends Model
{
    use HasFactory,SoftDeletes;

    static $types =[
        1=>'trending',
        2=>'best_Sellers',
        3=>'top_rated',
    ];

    public function product_name()
    {
        return $this->belongsTo(ProductName::class,'product_id','product_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
