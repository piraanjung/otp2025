<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QaAccTrans extends Model
{
    protected $connection = 'qa'; 

    // ชื่อตาราง
    protected $table = 'acc_transactions';

    public function invoice(){
        return $this->hasMany(QaInvoice::class, 'acc_trans_id_fk');
    }

}
