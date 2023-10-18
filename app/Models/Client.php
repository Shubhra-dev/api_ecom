<?php

namespace App\Models;

use App\Traits\ActiveProperty;
use App\Traits\ScopeDateFilter;
use App\Traits\ScopeSearch;
use App\Traits\ScopeSort;
use App\Traits\TypeProperty;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, ActiveProperty, TypeProperty, ScopeDateFilter, ScopeSearch, ScopeSort;

    protected $guarded = [];
    public $timestamps = false;


    protected static function getTypes(){
        return [
            1 => 'Online',
            2 => 'Inside Dhaka',
            3 => 'Outside Dhaka',
        ];
    }
    
    public function scopeFilter($query)
    {
        return $query
            ->when(isset(request()->active), function($query) {
                $query->where('active', request()->active);
            });

    }



    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    public function user() 
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function price_category()
    {
        return $this->belongsTo(PriceCategory::class);
    }

}
