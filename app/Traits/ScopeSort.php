<?php

namespace App\Traits;
use Illuminate\Support\Str;

trait ScopeSort
{
    public function scopeSort($query, $sort = null, $order = null)
    {
        $sort ?: $sort = 'id';

        $order ?: $order = 'asc';

        if(Str::contains($sort, '.')) {
            $sort_by_array = explode('.', $sort);
            
            $sort = array_pop($sort_by_array);

            $relation = implode('.', $sort_by_array);

            $query->with($relation, function ($query) use ($sort, $order) {
                return $query->orderBy($sort, $order);
            });
        }

        return $query->orderBy($sort, $order);
    }
}