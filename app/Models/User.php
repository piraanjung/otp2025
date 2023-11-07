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
        // 'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    protected $table = 'users';

    public function user_profile()
    {
        return $this->hasOne('App\Models\User','user_id','id');
    }
    public function usercategory(){
        return $this->belongsTo('App\Usercategory', 'user_cat_id');
    }

    public function user_zone(){
        return $this->belongsTo(Zone::class,'zone_id', 'id');
    }

    public function user_subzone(){
        return $this->belongsTo(Subzone::class, 'subzone_id', 'id');
    }

    public function undertaker_subzone(){
        return $this->hasMany(UndertakerSubzone::class, 'twman_id');
    }

    public function usermeterinfos(){
        return $this->hasOne(UserMerterInfo::class, 'user_id', 'id');
    }
    public function user_province(){
        return $this->belongsTo(Province::class, 'province_code');
    }

    public function user_district(){
        return $this->belongsTo(District::class, 'district_code');
    }

    public function user_tambon(){
        return $this->belongsTo(Tambon::class, 'tambon_code');
    }
}
