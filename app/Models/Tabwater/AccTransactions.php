<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccTransactions extends Model
{
    use HasFactory;

    protected $table = 'acc_transactions';

    protected $fillable = ['id' , 'user_id_fk','vatsum', 'inv_no_fk', 'paidsum', 'totalpaidsum', 'cashier'];

    public function cashier_info(){
        return $this->belongsTo(User::class,'cashier', 'id');
    }

    public function invoice(){
        return $this->hasMany(Invoice::class,'acc_trans_id_fk');
    }
}
