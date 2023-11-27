<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMeterInfoOld extends Model
{
    use HasFactory;
    protected $table = "user_meter_infos_old";

    public function invoiceold()
    {
        return $this->hasMany(Invoice::class, 'user_id', 'user_id');
    }

    public function userprofile()
    {
        return $this->hasOne(UserProfile::class, 'user_id', 'user_id');
    }
}
