<?php

namespace App\Models\Tabwater;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwInvoiceTemp extends Model
{
    use HasFactory;
    protected $table = 'tw_invoice_temp';

    protected $fillable = [
                'id',
                'meter_id_fk',
                'inv_period_id_fk',
                'lastmeter',
                'inv_no',
                'reserve_meter',
                'inv_type',
                'currentmeter',
                'water_used',
                'paid',
                'vat',
                'totalpaid',
                'status',
                'recorder_id',
                'acc_trans_id_fk',
                'printed_time'
    ];

     public function invoice_period()
    {
        return $this->belongsTo(TwInvoicePeriod::class, 'inv_period_id_fk', 'id');
    }

    public function recorder()
    {
        return $this->belongsTo(User::class,'recorder_id', 'id');
    }

    public function usermeterinfos()
    {
        return $this->belongsTo(TwUsersInfo::class, 'meter_id_fk', 'meter_id');
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
