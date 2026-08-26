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
        'user_id',
        'org_id_fk',
        'reporter_name',
        'reporter_phone',
        'system_type',
        'issue_type',
        'custom_issue_type',
        'description',
        'latitude',
        'longitude',
        'photo_path',
        'status',
    ];

    protected $casts = [
        'photo_path' => 'array',
    ];

    // ความสัมพันธ์แบบ Many-to-Many กับ User (Staff) ผ่านตาราง tw_notify_staff
    public function staffs()
    {
        return $this->belongsToMany(User::class, 'tw_notify_staff', 'notify_id', 'user_id')
            ->withPivot('staff_status')
            ->withTimestamps();
    }

    public function notifyStaffs()
    {
        return $this->hasMany(TwNotifyStaffs::class, 'notify_id');
    }
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
    // public function staffs()
    // {
    //     return $this->belongsToMany(
    //         User::class,            // Model ปลายทาง (Staff/User)
    //         'tw_notify_staff',      // ชื่อตาราง Pivot (ตามภาพ)
    //         'notify_id',            // FK ใน pivot table ที่ชี้มาหา tw_notifies.id
    //         'user_id'               // FK ใน pivot table ที่ชี้ไปหา users.id
    //     )
    //     ->withPivot('staff_status')
    //     ->withTimestamps();
    // }

    public function assignedStaff()
    {
        return $this->belongsToMany(User::class, 'notify_staff', 'notify_id', 'user_id')
            ->withPivot('staff_status') // ดึงสถานะเฉพาะของ Staff ต่องานนั้นมาด้วย
            ->withTimestamps();
    }
}
