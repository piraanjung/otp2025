<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetYear extends Model
{
    use HasFactory;
    protected $fillable = [
        "id",
        "budgetyear_name",
        'startdate',
        'enddate',
        'status'
    ];
    protected $table = "budget_year";

    public function invoicePeriod()
    {
        return $this->hasMany('App\Models\InvoicePeriod', 'budgetyear_id', 'id');
    }
}
