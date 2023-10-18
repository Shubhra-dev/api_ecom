<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pricing extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public $timestamps = false;

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function getPriceNameAttribute()
    {
        return $this->price_categroy->name;
    }

    public function price_categroy()
    {
        return $this->belongsTo(PriceCategory::class, 'price_category_id');
    }
}
