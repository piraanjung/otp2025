<?php

namespace App\Models;

use App\Models\Tabwater\TwAccTransactions;
use App\Models\Tabwater\TwInvoicePeriod;
use App\Models\Tabwater\TwMeterInfos;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceHistoryNew extends Model
{
    use HasFactory;
    protected $fillable = [
        'inv_id',
        'inv_period_id_fk',
        'meter_id_fk',
        'lastmeter',
        'water_used',
        'inv_type',
        'paid',
        'vat',
        'totalpaid',
        'acc_trans_id_fk',
        'currentmeter',
        'status',
        'recorder_id'
    ];
    protected $table = 'invoice_history_new';

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
