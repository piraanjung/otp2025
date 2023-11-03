<?php

namespace App\Models;

use App\Models\Admin\UserProfile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $fillable = [
        'inv_period_id_fk',
        'meter_id_fk',
        'lastmeter',
        'currentmeter',
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

    public function accounting()
    {
        return $this->belongsTo(Account::class, 'receipt_id', 'id');
    }
    public function invoice_inv_pd_active()
    {
        return $this->hasOne(InvoicePeriod::class, 'inv_period_id_fk', 'id');
    }
}
