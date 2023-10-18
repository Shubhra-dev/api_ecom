<?php

namespace App\Models;

use App\Traits\ActiveProperty;
use App\Traits\ScopeDateFilter;
use App\Traits\ScopeSearch;
use App\Traits\ScopeSort;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PriceCategory extends Model
{
    use HasFactory, SoftDeletes, ScopeDateFilter, ScopeSearch, ScopeSort,  ActiveProperty;

    protected $guarded = [];

    static $Types  = [
        0 => "Doctor",
        1 => "Special",
        2 => "Client",
    ];
    // static $GiftIds  = [7];
    static $GiftIds  = [7];

    public function pricings()
    {
        return $this->hasMany(Pricing::class);
    }

    public function scopeFilter($query)
    {
        return $query
            ->when(isset(request()->active), function ($query) {
                $query->where("active", request()->active);
            });
    }

    public function client_customer()
    {
        return $this->belongsToMany(Customer::class, 'client');
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }
}
