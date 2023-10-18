<?php

namespace App\Models;

use App\Traits\ScopeDateFilter;
use App\Traits\ScopeSearch;
use App\Traits\ScopeSort;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnMemo extends Model
{
    use HasFactory, ScopeDateFilter, ScopeSearch, ScopeSort;

    protected $guarded = [];

    // public function products()
    // {
    //     return $this->belongsToMany(Product::class)->withPivot('quantity');
    // }
    public function scopeFilter($query)
    {
        return $query
        ->when(isset(request()->outlet), function($query) {
            $query->where('outlet_id', request()->outlet);
        });
    }
    
    public function products()
    {
        return $this->belongsToMany(Product::class, 'return_product','return_memo_id','product_id')->withPivot('quantity');
    }
    public function product_names()
    {
        return $this->belongsToMany(ProductName::class, 'return_product','return_memo_id','product_id','id','product_id')->withPivot('quantity');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
