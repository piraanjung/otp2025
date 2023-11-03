<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UndertakerSubzone extends Model
{
    use HasFactory;
    protected $table = 'undertaker_subzone';

    public function user_meter_infos(){
        return $this->hasMany('App\UserMeterInfos', 'subzone_id');
    }

    public function user(){
        return $this->belongsTo('App\User', 'twman_id');
    }
    public function user_profile()
    {
        return $this->belongsTo('App\UserProfile','twman_id', 'user_id');
    }

    public function subzone(){
        return $this->belongsTo('App\Subzone','subzone_id','id');
    }

}
