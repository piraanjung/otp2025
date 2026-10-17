<?php

namespace App\Models\AnnualTrash;

use App\Models\Admin\BudgetYear as AdminBudgetYear;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnualTrashPayratePerMonth extends Model
{
    use HasFactory;

    protected $table = 'annual_trash_payrate_permonth';

    protected $fillable = [
        'id',
        'kp_usergroup_idfk',
        'budgetyear_idfk',
        'payrate_permonth',
        'vat',
        'status',
        'deleted'
    ];
    // public function kp_usergroup()
    // {
    //     return $this->belongsTo(KpUserWastePreference::class, 'kp_usergroup_idfk', 'id');
    // }

    public function budgetyear()
    {
        return $this->belongsTo(AdminBudgetYear::class, 'budgetyear_idfk', 'id');
    }
}
