<?php

namespace App\Models;

use App\Traits\ActiveProperty;
use App\Traits\ScopeDateFilter;
use App\Traits\ScopeSearch;
use App\Traits\ScopeSort;
use App\Traits\TypeProperty;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Outlet extends Model
{
    use HasFactory, SoftDeletes, ActiveProperty, TypeProperty, ScopeDateFilter, ScopeSearch, ScopeSort;

    public $timestamps = false;

    protected $guarded = [];

    protected static function getTypes()
    {
        return [
            1 =>'Godown',
            2 =>'Sale point'
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

    public function accounts()
    {
        return $this->morphMany(Account::class, 'accountable');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function circulations()
    {
        return $this->morphMany(Circulation::class, 'destinationable');
    }

    public function storages() 
    {
        return $this->hasMany(Storage::class);
    }
    /**
     * Get all of the comments for the Outlet
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class );
    }
    /**
     * Get the user associated with the Outlet
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function inventory_on_date(): HasOne
    {
        return $this->hasOne(Inventory::class)->where('opening_time', '>=' , strtotime(request()->from ?? date('Y-m-d')) )
            ->where('opening_time', '<' , strtotime(request()->to. " +1 days" ?? date('Y-m-d'). " +1 days") );
    }
}
