<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserOld extends Model
{
    use HasFactory;

    protected $table = 'users_old';
    public function userprofile()
    {
        return $this->hasOne(UserProfile::class, 'user_id', 'id');
    }
}
