<?php

namespace App\Models\FoodWaste;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodwastIotbox extends Model
{
   use HasFactory;

    // กำหนดชื่อตารางถ้าชื่อ Model ไม่ตรงตามข้อกำหนดของ Laravel (ในที่นี้คือ 'foodwast_iotboxes')
    protected $table = 'foodwast_iotboxes';

    // กำหนดคอลัมน์ที่อนุญาตให้ใส่ข้อมูล

    protected $fillable = [
        'id',
        'iotbox_code',
        'temp_humid_sensor',
        'gas_sensor',
        'weight_sensor',
    ];
}
