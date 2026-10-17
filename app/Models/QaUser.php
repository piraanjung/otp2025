<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QaUser extends Model
{
protected $connection = 'qa'; 

    // ชื่อตาราง
    protected $table = 'users';

    // 2. สร้าง Relationship ไปยังตาราง Invoice
    public function user_meter_info(): HasMany
    {
        // แม้จะอยู่คนละ DB แต่ถ้าอยู่บน MySQL Server เดียวกัน หรือตั้งค่า Connection ถูกต้อง Eloquent จะหาเจอครับ
        return $this->hasMany(QaUsermeterInfos::class, 'user_id', 'id');
    }

    
    
}
