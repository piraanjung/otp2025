<?php

namespace App\Models\Tabwater;

use App\Http\Controllers\FunctionsController;
use App\Models\Admin\UserProfile;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Invoice extends Model
{
    use HasFactory;
     public $timestamps = false;
    protected $fillable = [
        'inv_id',
        'inv_period_id_fk',
        'meter_id_fk',
        'lastmeter',
        'water_used',
        'reserve_meter',
        'inv_type',
        'paid',
        'vat',
        'totalpaid',
        'acc_trans_id_fk',
        'currentmeter',
        'recorder_id',
        'status',
        'created_at',
        'updated_at'
    ];
    protected $table = 'invoice';

    public function invoice_period()
    {
        return $this->belongsTo(InvoicePeriod::class, 'inv_period_id_fk', 'id');
    }

    public function recorder()
    {
        return $this->belongsTo(User::class,'recorder_id', 'id');
    }

    public function usermeterinfos()
    {
        return $this->belongsTo(UserMerterInfo::class, 'meter_id_fk', 'meter_id');
    }

    public function acc_transactions()
    {
        return $this->belongsTo(AccTransactions::class, 'acc_trans_id_fk', 'id');
    }
    public function invoice_inv_pd_active()
    {
        return $this->hasOne(InvoicePeriod::class, 'inv_period_id_fk', 'id');
    }

    public function inv_no($meter_id, $db = 'mysql')
    {
        // $meter_id;
        $funcCtrl = new FunctionsController();
        $budgetyear = DB::connection($db)->table('budget_year')->where('status', 'active')->get('id');
        $budgetyear_id_str = $budgetyear[0]->id < 10 ? "0" . $budgetyear[0]->id : $budgetyear[0]->id;

        $inv_owe = DB::connection($db)->table('invoice')->whereIn('status', ['owe'])->where('meter_id_fk', $meter_id)->get()->last();
        //`check ว่าเป็นรอบบิลเริ่มต้น
        $inv_no = '01';
        // $prevInvPeriod = DB::connection($db)->table('invoice_period')->where('status', 'inactive')->get();
        // if(collect($prevInvPeriod)->isEmpty()){
        //     return $budgetyear_id_str . "" . $inv_no . "" . $funcCtrl->createNumberString($meter_id);
        // }
        if (collect($inv_owe)->isNotEmpty()) {

         return   $inv_no= substr($inv_owe->inv_no, 2, 2);
        } else {
            $inv = DB::connection($db)->table('invoice')->where('status', 'paid')->where('meter_id_fk', $meter_id)->get(['inv_no', 'meter_id_fk','status'])->last();
            try{
                $inv_no = intval(substr($inv->inv_no, 2, 2))+1 < 10 ? "0" . intval(substr($inv->inv_no, 2, 2))+1 : intval(substr($inv->inv_no, 2, 2))+1 ;
            }catch(Exception $e){
                $inv_no = '01';
            }
        }
        return $budgetyear_id_str . "" . $inv_no . "" . $funcCtrl->createNumberString($meter_id);
    }
}
