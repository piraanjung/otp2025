<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class Kiosk extends Model
{
    use HasFactory;
    // ⚠️ สำคัญมาก: บอก Laravel ว่า PK ของเราไม่ใช่ตัวเลข Auto Increment
    protected $primaryKey = 'id';

    protected $table = 'kiosks';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'lat',
        'lng',
        'status',
        'total_waste_count',
        'current_user_id',
        'last_online_at'
    ];

    protected $dates = ['last_online_at'];

    // --- Relationships ---

    // เชื่อมกับ User (ดูว่าใครกำลังใช้อยู่)
    public function currentUser()
    {
        return $this->belongsTo(User::class, 'current_user_id');
    }



    // --- Helper Attributes ---

    // เช็คว่าตู้นี้ Online อยู่ไหม? (ถ้าไม่ส่งสัญญาณมาเกิน 5 นาที ถือว่า Offline)
    // เรียกใช้ใน Blade: $kiosk->is_online
    public function getIsOnlineAttribute()
    {
        if (!$this->last_online_at) return false;
        return Carbon::parse($this->last_online_at)->diffInMinutes(now()) < 5;
    }
}
