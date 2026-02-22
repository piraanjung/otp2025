<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Traits\BelongsToOrganization;
class Zone extends Model
{
    use HasFactory;
    use BelongsToOrganization;
    protected $fillable = ["zone_name","org_id_fk", "tambon_id", "location","status"];

    public function subzone(){
        return $this->hasMany(Subzone::class, 'zone_id');
    }

    public function tambon(){
        return $this->belongsTo(Tambon::class, 'tambon_id');
    }

    public static function getOrgSubzone($type){
         $org_subzones = Zone::where('tambon_id', Auth::user()->tambon_code)
             ->with(['subzone' => function($q){
                $q->where('status', 'active');
             }])->get();
             if($type == 'array'){
                return collect($org_subzones)->map(function($_subzone){
                    return $_subzone->subzone[0];
                });
            }
            //id
             return collect($org_subzones)->map(function($_subzone){
                return ['id' =>$_subzone->subzone[0]->id];
             });
    }

}
