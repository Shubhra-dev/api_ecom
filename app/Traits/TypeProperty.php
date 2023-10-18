<?php

namespace App\Traits;

trait TypeProperty
{

    protected static function getTypes(){ }

    public function getValueOfTypeAttribute()
    {
        $types = self::getTypes();

        return $types[$this->type] ?? '';
    }

}