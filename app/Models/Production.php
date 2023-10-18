<?php

namespace App\Models;

use App\Traits\ActiveProperty;
use App\Traits\ScopeSearch;
use App\Traits\ScopeSort;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Production extends Model
{
    use HasFactory, SoftDeletes, ActiveProperty, ScopeSearch, ScopeSort;

    protected $guarded = [];

    public function scopeFilter($query)
    {
        return $query
            ->when(isset(request()->publisher), function ($query) {
                $query->where("publisher_id", request()->publisher);
            })
            ->when(isset(request()->active), function ($query) {
                $query->where("active", request()->active);
            });
    }

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    public function publisher()
    {
        return $this->belongsTo(Publisher::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
