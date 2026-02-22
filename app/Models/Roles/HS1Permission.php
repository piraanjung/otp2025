<?php

namespace App\Models\Roles;

use Spatie\Permission\Models\Permission as SpatiePermission;
class HS1Permission extends SpatiePermission
{
    // กำหนดให้ Model นี้ใช้ Connection 'envsogo_hs1'
    protected $connection = 'envsogo_hs1'; 
}