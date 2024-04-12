<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
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
        'password',
        'followers',
        'code',
        'expire_at',
        'role'
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


    public function generateCode()
    {
        $this->timestamps = false;
        $this->code = rand(10000, 99999);
        $this->expire_at = now()->addMinute(5);
        $this->save();
    }

    public function employee()
    {   // telling user model that he's related to phone table
        return $this->hasOne(employee::class);
    }

    public function company()
    {
        return $this->hasOne(company::class);
    }

    public function Application()
    {
        return $this->hasMany(Application::class);
    }
    public function Favorite()
    {
        return $this->hasMany(Favorite::class);
    }

}