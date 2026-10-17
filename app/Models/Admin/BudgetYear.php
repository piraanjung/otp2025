<?php

namespace App\Models\Admin;

use App\Models\Tabwater\InvoicePeriod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetYear extends Model
{
    use HasFactory;
    protected $fillable = [
        "org_id_fk",
        "budgetyear_name",
        'startdate',
        'enddate',
        'status'
    ];
    protected $table = "budget_year";

    public function invoice_period()
    {
        return $this->hasMany(InvoicePeriod::class, 'budgetyear_id', 'id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'org_id_fk', 'id');
    }
}
