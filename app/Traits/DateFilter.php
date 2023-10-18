<?php

namespace App\Traits;

trait DateFilter
{
    public function dateFilter($column = 'created_at')
    {
        $this->getQuery()
            ->when(request()->from, function ($query, $date_from) use ($column) {
                $query->whereDate($column, '>=', $date_from);
            })
            ->when(request()->to, function ($query, $date_to) use ($column) {
                $query->whereDate($column, '<=', $date_to);
            });

        return $this;
    }
}