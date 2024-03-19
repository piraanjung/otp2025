<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccTransactions extends Model
{
    use HasFactory;

    protected $table = 'acc_transactions';

    protected $fillable = ['id' , 'user_id_fk','vatsum', 'paidsum', 'totalpaidsum', 'cashier'];

    public function cashier_info(){
        return $this->belongsTo(User::class,'cashier', 'id');
    }
}
