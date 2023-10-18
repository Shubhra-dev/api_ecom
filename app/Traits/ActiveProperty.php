<?php

namespace App\Traits;

trait ActiveProperty
{
    protected static function getActiveProperties(){
        return [
            1 => 'Yes',
            0 => 'No',
        ];
    }

    public function getValueOfActiveAttribute()
    {
        $actives = self::getActiveProperties();

        return $actives[$this->active] ?? '';
    }
}