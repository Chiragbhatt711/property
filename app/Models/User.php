<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'user_id',
        'email',
        'email_verified_at',
        'password',
        'user_type',
        'remember_token',
        'phone',
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

    // protected $appends = ['anchor_users'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }


    // public function getAnchorUsersAttribute()
    // {
    //     if(isset($this->anchor_id))
    //     {
    //         return User::join('anchor_mates','anchor_mates.user_id','users.id')
    //                     ->where('anchor_mates.anchor_id',$this->anchor_id)
    //                     ->where('anchor_mates.role','mate')
    //                     ->where('anchor_mates.user_id','!=',$this->userid)
    //                     ->select('users.*')
    //                     ->get()->toArray();
    //     }
    //     return [];
    // }


    // public function products()
    // {
    //     return $this->hasMany(Product::class);
    // }
}
