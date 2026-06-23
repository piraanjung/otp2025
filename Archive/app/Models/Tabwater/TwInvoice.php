<?php

namespace App\Models\Tabwater;

use App\Models\User;
use App\Models\Tabwater\TwInvoicePeriod;
use App\Models\Tabwater\TwMeterInfos;
use App\Models\Tabwater\TwAccTransactions;
use App\Traits\BelongsToOrganization; // <--- เรียกใช้แค่ตัวนี้พอ
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TwInvoice extends Model
{
    // ใช้แค่ BelongsToOrganization ตัวเดียว (มันสลับ DB ให้แล้ว)
    use HasFactory, BelongsToOrganization; 

    protected $table = 'tw_invoice';
    public $timestamps = false;
    
    protected $fillable = [
        'id',
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
        'updated_at',
        'inv_no',
        'org_id_fk' // ต้องมี column นี้ใน DB ตามที่คุยกัน
    ];

    public function tw_meter_infos()
{
    return $this->belongsTo(TwMeterInfos::class, 'meter_id_fk', 'meter_id');
}
    public function invoice_period()
    {
        return $this->belongsTo(TwInvoicePeriod::class, 'inv_period_id_fk', 'id');
    }

    public function tw_acc_transactions()
    {
        return $this->belongsTo(TwAccTransactions::class, 'acc_trans_id_fk', 'id');
    }
    // ----------------------------------------------------------------------
    // ฟังก์ชัน Generate Invoice No (ปรับปรุงใหม่)
    // ----------------------------------------------------------------------
    public function generateInvNo($meter_id)
    {
        // $this->getConnectionName() เรียกใช้ได้เลย เพราะมาจาก Trait BelongsToOrganization
        $budgetyear = DB::connection($this->getConnectionName())
                        ->table('budget_year')
                        ->where('status', 'active')
                        ->first();

        $budget_id = $budgetyear ? $budgetyear->id : 0;
        $budgetyear_id_str = str_pad($budget_id, 2, '0', STR_PAD_LEFT);

        // ใช้ self:: เพื่อให้ Trait ทำงาน (สลับ DB + กรอง Org)
        $last_inv = self::where('meter_id_fk', $meter_id)
                        ->whereIn('status', ['owe', 'paid'])
                        ->latest('id')
                        ->first();

        $inv_running_no = '01';

        if ($last_inv) {
            $current_period_no = intval(substr($last_inv->inv_no, 2, 2));
            
            if ($last_inv->status == 'owe') {
                $inv_running_no = str_pad($current_period_no, 2, '0', STR_PAD_LEFT);
            } else {
                $inv_running_no = str_pad($current_period_no + 1, 2, '0', STR_PAD_LEFT);
            }
        }

        $meter_suffix = $this->createNumberString($meter_id);

        return $budgetyear_id_str . $inv_running_no . $meter_suffix;
    }

    private function createNumberString($number)
    {
        return str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}