<?php

namespace App\Traits;

trait ScopeSearch
{
    public function scopeSearch($query, array $columns, array $relations = [], $operator = 'regexp')
    {
        return $query
            ->when(isset(request()->search), function ($query) use ($columns, $relations, $operator) {
                $query->where(function ($query) use ($columns, $relations, $operator) {
                    foreach($columns as $column) {
                        if($column) {
                            $query->orWhere($column, $operator, request()->search);
                        }
                    }

                    foreach($relations as $relation) {
                        if($relation) {
                            $query->orWhereHas(explode(':', $relation)[0], function ($query) use ($relation, $operator) {
                                foreach(explode(',', explode(':', $relation)[1] ?? '') as $relation_column) {
                                    if($relation_column) {
                                        $query->where($relation_column, $operator, request()->search);
                                    }
                                }
                            });
                        }
                    }
                });
            });
    }
}