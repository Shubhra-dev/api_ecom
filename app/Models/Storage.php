<?php

namespace App\Models;

use App\Traits\ProductSearch;
use App\Traits\ScopeDateFilter;
use App\Traits\ScopeSort;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use tidy;

class Storage extends Model
{
    use HasFactory, SoftDeletes, ScopeDateFilter, ScopeSort, ProductSearch;

    protected $guarded = [];

    // public $timestamps = false;

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function scopeFilter($query)
    {
        return $query
            ->when(isset(request()->your_outlet), function ($query) {
                $query->where('outlet_id', request()->your_outlet);
            });
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    // public function getQuantityAttribute()
    // {
    //     return $this->quantity == NULL ? 0 : $this->quantity;
    // }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }


    

    public function circulations()
    {
        return $this->hasMany(Circulation::class);
    }

    public function productRequests()
    {
        return $this->hasMany(ProductRequest::class, 'storage_id', 'id');
    }
}
