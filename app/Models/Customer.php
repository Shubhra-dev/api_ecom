<?php

namespace App\Models;

use App\Http\Resources\AuthUserResource;
use App\Traits\ScopeDateFilter;
use App\Traits\ScopeSearch;
use App\Traits\ScopeSort;
use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Model
{
    use HasApiTokens, HasFactory, SoftDeletes, Timestamp, ScopeSort, ScopeSearch, ScopeDateFilter;

    protected $guarded = [];
    protected $appends = ['unique_client_id'];

     /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    const INITIAL = 143572;

    public function scopeFilter($query)
    {
        return $query
            ->when(isset(request()->active), function($query) {
                $query->where('active', request()->active);
            });

    }

    public function getUniqueClientIdAttribute() {
        // return 313;
        return "B". (string) (self::INITIAL + ($this->id*333));
    }

    public function address()
    {
        return $this->hasOne(AddressesOf::class, 'customer_id');
    }

    public function genesis_info()
    {
        // if ($this->memo_type == 3)
        return $this->hasOne(GenesisCustomerInfo::class, 'customer_id');
    }

    public function clients()
    {
       return $this->hasMany(Client::class);
    }

    // public function client_price_types()
    // {
    //    return $this->belongsToMany(PriceCategory::class, 'client');
    // }
    public function setAndGetLoginResponse($token = null, $additional = [])
    {
        AuthUserResource::withoutWrapping();
        

        if ($token === null) {
            $token = $this->loginAndGetToken();
        }
        
        return [
            'user'  => (new AuthUserResource($this)),
            'token' => $token,
            'tokenHash' => base64_encode($token),
            'access' => $access ?? [],
            // 'abilities' => $permissions,
            // 'branchDomains' => $branches,
        ] + $additional;
    }

    public function loginAndGetToken()
    {
        return $this->createToken(request()->ip())->plainTextToken;
    }
}
