<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AddressesOf extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'addresses_of';

    protected $guarded = [];

    public $timestamps = false;

    public function area ()
    {
        return $this->belongsTo(Area::class);
    }

}
