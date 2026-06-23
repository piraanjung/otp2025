<?php

namespace App\Models\Admin;

use App\Models\Admin\Zone;
use App\Models\Tabwater\UndertakerSubzone;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tabwater\TwMeterInfos;
use App\Traits\BelongsToOrganization;
class Subzone extends Model
{
    use HasFactory;
    use BelongsToOrganization;
    protected $fillable = [ "id", "zone_id","subzone_name","status",'org_id_fk'];
    protected $table = "subzones";
    public function zone()
    {
        return $this->belongsTo(Zone::class, 'zone_id', 'id');
    }

    public function users_in_subzone(){
        return $this->hasMany(TwMeterInfos::class,'undertake_subzone_id', 'id');
    }

    public function organization(){
        return $this->belongsTo(Organization::class, 'org_id_fk');
    }

    public function undertaker_subzone(){
        return $this->hasMany(UndertakerSubzone::class, 'subzone_id');
    }

}
