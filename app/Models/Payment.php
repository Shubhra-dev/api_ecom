<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payment';
    protected $guarded  = [];
    // public $timestamps = false;
    
    const PaymentMethod = [
        1 => "Cash",
        2 => "Bkash",
        3 => "Nagad",
        4 => "Master Card",
        5 => "Visa",
        6 => "SIBL",
        7 => "AIBL",
        8 => "Rocket",
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
