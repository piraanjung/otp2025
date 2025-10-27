<?php

namespace App\Models\Roles;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role as SpatieRole;
class HS1Role extends SpatieRole
{
    // กำหนดให้ Model นี้ใช้ Connection 'envsogo_hs1'
    protected $connection = 'envsogo_hs1'; 
}
