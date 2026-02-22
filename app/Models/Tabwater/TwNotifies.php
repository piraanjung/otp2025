<?php

namespace App\Models\Tabwater;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwNotifies extends Model
{
    protected $table = 'tw_notifies'; // เปลี่ยนชื่อตารางเป็น notifies

    /**
     * กำหนดฟิลด์ที่อนุญาตให้ Mass Assignment ได้
     */
    protected $fillable = [
        'user_id',          // ID ของผู้แจ้งเหตุ (ผู้ใช้งานทั่วไป)
        'staff_id',         // ID ของ Staff ผู้รับงาน (จะเป็น null ตอนแรก)
        'issue_type',
        'description',
        'latitude',
        'longitude',
        'photo_path',
        'status',           // สถานะของงาน (pending, processing, complete, cancel)
    ];

    /**
     * ความสัมพันธ์: ดึงข้อมูลผู้แจ้งเหตุ
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * ความสัมพันธ์: ดึงข้อมูล Staff ผู้รับงาน (สมมติว่า Staff ก็คือ User Model)
     */
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function assignedStaff()
{
    return $this->belongsToMany(User::class, 'notify_staff', 'notify_id', 'user_id')
                ->withPivot('staff_status') // ดึงสถานะเฉพาะของ Staff ต่องานนั้นมาด้วย
                ->withTimestamps();
}
}
