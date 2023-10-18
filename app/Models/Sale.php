<?php

namespace App\Models;

use App\Traits\ScopeDateFilter;
use App\Traits\ScopeSearch;
use App\Traits\ScopeSort;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory, SoftDeletes, ScopeDateFilter, ScopeSearch, ScopeSort;

    protected $guarded = [];

    // protected $appends = ['paid','due'];

    public function scopeFilter($query)
    {
        return $query
        ->when(isset(request()->outlet), function($query) {
            $query->where('outlet_id', request()->outlet);
        });
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function genesis_info()
    {
        return $this->hasOne(GenesisCustomerInfo::class);
    }

    public function sale_details()
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function parent_items()
    {
        return $this->hasMany(SaleDetail::class)
            ->whereNull('sale_details.parent_id');
    }

    public function return_memos()
    {
        return $this->hasMany(ReturnMemo::class);
    }

    /**
     * Get all of the comments for the Sale
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function products(): HasManyThrough
    {
        return $this->hasManyThrough(Product::class, SaleDetail::class, 'sale_id', 'id', 'id', 'product_id' );
    }
}
