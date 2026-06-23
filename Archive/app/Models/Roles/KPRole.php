<?php

namespace App\Models\Roles;

use Spatie\Permission\Models\Role as SpatieRole;

class KPRole extends SpatieRole
{
    // กำหนดให้ Model นี้ใช้ Connection 'envsogo_kp1'
    protected $connection = 'envsogo_kp1'; 

}
