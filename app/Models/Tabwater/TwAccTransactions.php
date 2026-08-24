<?php

namespace App\Models\Tabwater;

use App\Models\Admin\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwAccTransactions extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'tw_acc_transactions';

    protected $fillable = ['id', 'org_id_fk', 'meter_id_fk', 'vatsum', 'reserve_meter_sum', 'paidsum', 'totalpaidsum', 'cashier'];

    public function cashier_info()
    {
        return $this->belongsTo(User::class, 'cashier', 'id');
    }

    public function org()
    {
        return $this->hasOne(Organization::class, 'org_id_fk');
    }

    public function invoice()
    {
        return $this->hasMany(TwInvoice::class, 'acc_trans_id_fk');
    }
}
