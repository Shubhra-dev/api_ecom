<?php

namespace App\Models;

use App\Traits\ActiveProperty;
use App\Traits\ScopeDateFilter;
use App\Traits\ScopeSearch;
use App\Traits\ScopeSort;
use App\Traits\WithProductRelations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use HasFactory, ActiveProperty, ScopeSort, ScopeSearch, ScopeDateFilter, SoftDeletes, WithProductRelations;
    protected $guarded = [];

    public function package_products()
    {
        return $this->hasMany(PackageProduct::class);
    }


    public function product()
    {
        return $this->morphOne(Product::class, 'productable');
    }

}
