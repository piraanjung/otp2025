<?php

namespace App\Models\Tabwater;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwInvoiceTemp extends Model
{
    use HasFactory;
    protected $table = 'tw_invoice_temp';
    public $timestamps = false;
    protected $fillable = [
                'id',
                'meter_id_fk',
                'inv_period_id_fk',
                'lastmeter',
                'reserve_meter',
                'currentmeter',
                'water_used',
                'paid',
                'vat',
                'totalpaid',
                'status',
                'recorder_id',
                'inv_no',
                'acc_trans_id_fk',
                'printed_time',
                'comment'
    ];

     public function invoice_period()
    {
        return $this->belongsTo(TwInvoicePeriod::class, 'inv_period_id_fk', 'id');
    }

    public function recorder()
    {
        return $this->belongsTo(User::class,'recorder_id', 'id');
    }

    public function tw_meter_infos()
    {
        return $this->belongsTo(TwMeterInfos::class, 'meter_id_fk', 'meter_id');
    }

    public function acc_transactions()
    {
        return $this->belongsTo(TwAccTransactions::class, 'acc_trans_id_fk', 'id');
    }
    public function invoice_inv_pd_active()
    {
        return $this->hasOne(TwInvoicePeriod::class, 'inv_period_id_fk', 'id');
    }
}
