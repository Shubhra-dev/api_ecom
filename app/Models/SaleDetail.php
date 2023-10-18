<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    public static $sale_id = Null;

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
    public function price_category()
    {
        return $this->belongsTo(PriceCategory::class,'price_type');
    }
    public function sold_package_products()
    {
        return $this->hasMany(SaleDetail::class,'parent_id','product_id')
        ->where('sale_id',self::$sale_id);
    }
}
