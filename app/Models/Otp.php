<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    use HasFactory;

    protected $table = 'otp';

    protected $guarded = [];

    const CREATED_AT = null;

    protected $primaryKey = 'phone';

    public function setUpdatedAtAttribute($value)
    {
        $this->attributes['updated_at'] = $value;

        $this->attributes['count'] = (int) $this->count + 1;
    }
}
