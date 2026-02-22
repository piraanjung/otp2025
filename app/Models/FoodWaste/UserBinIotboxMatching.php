<?php

namespace App\Models;

use App\Models\FoodWaste\FoodwasteBin;
use App\Models\FoodWaste\FoodWasteUserPreference;
use App\Models\FoodWaste\FoodwastIotbox;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBinIotboxMatching extends Model
{
    use HasFactory;

    protected $table = 'user_bin_iotbox_matchings';

    // คอลัมน์ที่อนุญาตให้ Mass Assignment
    protected $fillable = [
        'fw_user_id_fk',
        'bin_id_fk',
        'iotbox_id_fk',
    ];

    // ความสัมพันธ์กับ FoodwasteUserPreference (สมมติชื่อ Model)
    public function user()
    {
        return $this->belongsTo(FoodWasteUserPreference::class, 'fw_user_id_fk');
    }

    // ความสัมพันธ์กับ FoodwasteBin
    public function bin()
    {
        return $this->belongsTo(FoodwasteBin::class, 'bin_id_fk');
    }

    // ความสัมพันธ์กับ FoodwastIotbox
    public function iotbox()
    {
        return $this->belongsTo(FoodwastIotbox::class, 'iotbox_id_fk');
    }
}