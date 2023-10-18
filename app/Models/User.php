<?php

namespace App\Models;

use App\Http\Resources\AuthUserResource;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
    ];

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
