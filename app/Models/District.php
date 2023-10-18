<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    public function areas()
    {
        return $this->hasMany(Area::class);
    }

    public function division ()
    {
        return $this->belongsTo(Division::class);
    }
}
