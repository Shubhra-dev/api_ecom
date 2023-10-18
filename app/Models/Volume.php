<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Volume extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = [];
    
    public function scopeFilter($query)
    {
        return $query
            ->when(isset(request()->active), function($query) {
                $query->where("active", request()->active);
            });
    }

    public function products()
    {
        return $this->morphMany(Product::class, 'productable');
    }

    public  function version()
    {
        return $this->belongsTo(Version::class);
    }
}
