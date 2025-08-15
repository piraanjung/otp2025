<?php

namespace App\Models\Tabwater;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceHistoty extends Model
{
    use HasFactory;

    protected $table = 'invoice_history';

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

}
