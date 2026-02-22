<?php

namespace App\Models\Admin;


use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Admin\ManagesTenantConnection;

class SuperAdmin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles,ManagesTenantConnection; // เพิ่ม HasRoles ตรงนี้
    use Notifiable;
    protected $table = 'super_admin'; // ระบุชื่อตาราง

    protected $fillable = [
        'username',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
