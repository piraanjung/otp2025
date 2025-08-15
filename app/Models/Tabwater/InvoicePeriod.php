<?php

namespace App\Models\Tabwater;

use App\Models\Admin\BudgetYear;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicePeriod extends Model
{
    use HasFactory;

    protected $fillable = [ "inv_p_name","budgetyear_id","startdate","enddate","status"];
    protected $table = "invoice_period";

    public function budgetyear()
    {
        return $this->belongsTo(BudgetYear::class, 'budgetyear_id', 'id');
    }
    public function get_curr_inv_pd(){
        return InvoicePeriod::where('status','=', 'active')->first();
    }
}
