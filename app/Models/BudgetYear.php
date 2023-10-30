<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetYear extends Model
{
    use HasFactory;
    protected $fillable = [
        "budgetyear",
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
