<?php

namespace App\Models;

use App\Traits\ScopeDateFilter;
use App\Traits\ScopeSearch;
use App\Traits\ScopeSort;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnProduct extends Model
{
    use HasFactory, ScopeDateFilter, ScopeSearch, ScopeSort;

    protected $table = 'return_product';
    public $timestamps = false;


    public function return_memo()
    {
        return $this->belongsTo(ReturnMemo::class);
    }
}
