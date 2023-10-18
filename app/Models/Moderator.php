<?php

namespace App\Models;

use App\Traits\ScopeDateFilter;
use App\Traits\ScopeSearch;
use App\Traits\ScopeSort;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Moderator extends Model
{
    use HasFactory, SoftDeletes, ScopeSort, ScopeSearch, ScopeDateFilter;

    protected $guarded = [];

    static $name = false;

    public function scopeFilter($query)
    {
        return $query
            ->when(isset(request()->active), function($query) {
                $query->where('active', request()->active);
            });

    }

    // static $appends ['product_name'];
    // protected $appends = ['mentor_type_name','author_name'];




    public function getMentorTypeNameAttribute()
    {
        return $this->moderators_type->name ?? '';
    }
    public function getAuthorNameAttribute()
    {
        return $this->author->name ?? '';
    }

    public function moderators_type()
    {
        return $this->belongsTo(ModeratorType::class, 'moderator_type','id');
    }
    public function author()
    {
        return $this->belongsTo(Author::class);
    }

}

