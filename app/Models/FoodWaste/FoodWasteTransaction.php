<?php

namespace App\Models\FoodWaste;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class FoodWasteTransaction extends Model
{

    protected $table = 'foodwaste_transactions';
    protected $fillable = [
        'fw_pref_id_fk',
        'waste_log_id',       // <--- ใส่กลับเข้ามาแล้ว
        'transaction_type',
        'points',
        'amount',
        'note',
        'staff_id'
    ];

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    // เพิ่ม Relation ไปหาตาราง Log ขยะ
    public function wasteLog()
    {
        return $this->belongsTo(FoodWasteLog::class, 'waste_log_id');
    }

    public function preference()
    {
        // อ้างอิง Foreign Key 'fw_pref_id_fk' ไปหา Primary Key 'id' ของตาราง foodwaste_user_preferences
        return $this->belongsTo(FoodWasteUserPreference::class, 'fw_pref_id_fk', 'id');
    }

    public function account()
    {
        return $this->hasOne(FoodWasteAccount::class, 'fw_pref_id_fk');
    }

    public function transactions()
    {
        return $this->hasMany(FoodWasteTransaction::class, 'fw_pref_id_fk');
    }
}
