<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $table = 'user_profile';

    public function user()
    {
        return $this->belongsTo(UserOld::class, 'user_id');
    }

    public function invoice_old(){
        return $this->hasMany(InvoiceOld::class,'user_id','user_id');
    }

    public function invoice_history(){
        return $this->hasMany(InvoiceHistoty::class,'user_id','user_id');
    }

    public function usermeter_info_old(){
        return $this->hasMany(UserMeterInfoOld::class,'user_id','user_id');
    }
}
