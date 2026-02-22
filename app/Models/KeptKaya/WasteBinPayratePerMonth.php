<?php

namespace App\Models\KeptKaya;

use App\Models\Admin\BudgetYear as AdminBudgetYear;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WasteBinPayratePerMonth extends Model
{
    use HasFactory;

    protected $table = 'kp_waste_bin_payrate_permonth';

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
        return $this->belongsTo(KpUserWastePreference::class, 'kp_usergroup_idfk','id');
    }

    public function budgetyear(){
        return $this->belongsTo(AdminBudgetYear::class, 'budgetyear_idfk','id');
    }
}
