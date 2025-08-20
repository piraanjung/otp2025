<?php

namespace App\Models\Admin;

use App\Http\Controllers\Tabwater\UserMeterInfosController;
use App\Models\Admin\Zone;
use App\Models\UndertakerSubzone;
use App\Models\UserMerterInfo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subzone extends Model
{
    use HasFactory;

    protected $fillable = [ "zone_id","subzone_name","status"];
    protected $table = "subzones";
    public function zone()
    {
        return $this->belongsTo(Zone::class, 'zone_id', 'id');
    }

    public function users_in_subzone(){
        return $this->hasMany(\App\Models\Tabwater\UserMerterInfo::class,'undertake_subzone_id', 'id');
    }

    public function undertaker_subzone(){
        return $this->hasOne(\App\Models\Tabwater\UserMerterInfo::class, 'subzone_id');
    }
}
