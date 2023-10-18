<?php

namespace App\Traits;

trait ScopeDateFilter
{
    public function ScopeDateFilter($query, $column = 'created_at') 
    {
        return $query
            ->when(request()->from, function ($query, $date_from) use ($column) {
                $query->whereDate($column, '>=', $date_from);
            })
            ->when(request()->to, function ($query, $date_to) use ($column) {
                $query->whereDate($column, '<=', $date_to);
            });
    }
}