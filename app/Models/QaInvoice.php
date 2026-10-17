<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QaInvoice extends Model
{
    // ระบุ Connection เป็น 'qa' เช่นกัน (หรือถ้า Invoice อยู่ DB หลัก ก็ใส่เป็น 'mysql' หรือดึงออกได้เลย)
    protected $connection = 'qa'; 

    protected $table = 'invoice';
}