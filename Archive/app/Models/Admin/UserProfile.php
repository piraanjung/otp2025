<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id', 'subzone_id', 'district_code',
        'id_card', 'zone_id','province_code',
        'phone', 'gender', 'status',
        'address', 'tambon_code'
    ];

    protected $table = 'user_profile';

    public function zone(){
        return $this->belongsTo('App\Models\Zone', 'zone_id', 'id');
    }
}
