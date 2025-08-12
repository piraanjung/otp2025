<?php

namespace App\Models\KeptKaya;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpUserGroup extends Model
{
    use HasFactory;

    protected $table = "kp_usergroups";
    protected $fillable = [
        'id',
        'usergroup_name',
        'status',
        'deleted'
    ];


    public function kp_usergroup_payrate_permonth(){
        return $this->hasMany(KpUsergroupPayratePerMonth::class, 'kp_usergroup_idfk');
    }

  
}
