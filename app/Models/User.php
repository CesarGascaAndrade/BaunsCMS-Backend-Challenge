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
    const ROLE_SUPERADMIN = 'ROLE_SUPERADMIN';
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_WRITER = 'ROLE_WRITER';
    const ROLE_REVIEWER = 'ROLE_REVIEWER';
    const ROLE_USER = 'ROLE_USER';

    private const ROLES_HIERARCHY = [
        self::ROLE_SUPERADMIN => [
            self::ROLE_ADMIN,
            self::ROLE_WRITER,
            self::ROLE_REVIEWER,
            self::ROLE_USER
        ],
        self::ROLE_ADMIN => [
            self::ROLE_WRITER,
            self::ROLE_REVIEWER,
            self::ROLE_USER
        ],
        self::ROLE_WRITER => [
            self::ROLE_USER
        ],
        self::ROLE_REVIEWER => [
            self::ROLE_USER
        ],
        self::ROLE_USER => []
    ];

    public function isGranted($role)
    {
        return $role === $this->role || in_array(
            $role,
            self::ROLES_HIERARCHY[$this->role]
        );
    }

    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'enabled'
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

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function articles()
    {
        return $this->hasMany('App\Article');
    }

    public function comments()
    {
        return $this->hasMany('App\Comment');
    }

    public function categories()
    {
        return $this->belongsToMany('App\Category');
    }
}
