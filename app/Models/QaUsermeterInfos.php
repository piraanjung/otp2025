<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // หรือ HasOne แล้วแต่ดีไซน์ระบบ
use Illuminate\Database\Eloquent\Relations\HasOne; // หรือ HasOne แล้วแต่ดีไซน์ระบบ
class QaUsermeterInfos extends Model
{
    // 1. ระบุ Connection เป็น 'qa' ตามที่คุณตั้งค่าไว้ใน config/database.php
    protected $connection = 'qa'; 

    // ชื่อตาราง
    protected $table = 'user_meter_infos';

    // 2. สร้าง Relationship ไปยังตาราง Invoice
    public function invoices(): HasMany
    {
        // แม้จะอยู่คนละ DB แต่ถ้าอยู่บน MySQL Server เดียวกัน หรือตั้งค่า Connection ถูกต้อง Eloquent จะหาเจอครับ
        return $this->hasMany(QaInvoice::class, 'meter_id_fk', 'meter_id');
    }

    
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
