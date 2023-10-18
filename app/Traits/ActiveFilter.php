<?php

namespace App\Traits;

trait ActiveFilter
{
    public function activeFilter($column = 'active')
    {
        $this->getQuery()
            ->when(isset(request()->active), function ($query) use ($column) {
                $query->where($column, request()->active);
            });

        return $this;
    }
}