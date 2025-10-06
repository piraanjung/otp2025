<?php

namespace App\Models\Tabwater;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwAccTransactions extends Model
{
    use HasFactory;

    protected $table = 'tw_acc_transactions';

    protected $fillable = ['id' , 'meter_id_fk','vatsum', 'reserve_meter_sum', 'paidsum', 'totalpaidsum', 'cashier'];

    public function cashier_info(){
        return $this->belongsTo(User::class,'cashier', 'id');
    }

    public function invoice(){
        return $this->hasMany(TwInvoiceTemp::class,'acc_trans_id_fk');
    }
}
