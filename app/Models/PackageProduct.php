<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackageProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "package_product";

    public $timestamps = false; 

    protected $guarded = [];

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function package() {
        return $this->belongsTo(Package::class, 'package_id');
    }

}
