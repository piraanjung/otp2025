<?php

namespace App\Models\KeptKaya;

use App\Models\BudgetYear;
use App\Models\Keptkaya\KpUserGroup;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpUsergroupPayratePerMonth extends Model
{
    use HasFactory;

    protected $table = 'kp_usergroup_payrate_permonth';

    protected $fillable = [
        'id',
        'kp_usergroup_idfk',
        'budgetyear_idfk',
        'payrate_permonth',
        'vat',
        'status',
        'deleted'	
    ];
    public function kp_usergroup(){
        return $this->belongsTo(KpUserGroup::class, 'kp_usergroup_idfk','id');
    }

    public function budgetyear(){
        return $this->belongsTo(KpBudgetYear::class, 'budgetyear_idfk','id');
    }
}
