<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
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

    public function user_profile()
    {
        return $this->hasOne('App\Models\Admin\UserProfile','user_id','id');
    }
    public function usercategory(){
        return $this->belongsTo('App\Usercategory', 'user_cat_id');
    }

    public function invoice(){
        return $this->hasMany('App\Invoice', 'user_id');
    }

    public function undertaker_subzone(){
        return $this->hasMany('App\UndertakerSubzone', 'twman_id');
    }

    public function usermeter_info(){
        return $this->hasOne('App\Models\UserMerterInfo', 'user_id', 'id');
    }
}
