<?php

namespace Database\Seeders;

use App\Models\SystemModule;
use Illuminate\Database\Seeder;

class SystemModuleSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            [
                'system_type' => 'water',
                'title'       => 'งานระบบน้ำประปา',
                'icon'        => '💧',
                'description' => 'แจ้งท่อแตก น้ำไม่ไหล น้ำขุ่น หรือมาตรวัดน้ำชำรุด',
                'color'       => 'primary',
                'sort_order'  => 1,
            ],
            [
                'system_type' => 'recycle_trash',
                'title'       => 'ธนาคารขยะรีไซเคิล',
                'icon'        => '♻️',
                'description' => 'นัดรถรับซื้อขยะ สมุดคู่ฝาก หรือแจ้งถังขยะเต็ม',
                'color'       => 'success',
                'sort_order'  => 2,
            ],
            [
                'system_type' => 'food_waste',
                'title'       => 'ธนาคารขยะเศษอาหาร',
                'icon'        => '🍃',
                'description' => 'แจ้งปัญหาถังหมักมีกลิ่น เติมปุ๋ย หรืออุปกรณ์ชำรุด',
                'color'       => 'warning',
                'sort_order'  => 3,
            ],
            [
                'system_type' => 'public_health',
                'title'       => 'งานสาธารณสุข / ไฟฟ้า',
                'icon'        => '💡',
                'description' => 'แจ้งไฟถนนดับ ถังขยะทั่วไปล้น หรือเหตุรำคาญ',
                'color'       => 'danger',
                'sort_order'  => 4,
            ],
        ];

        foreach ($modules as $module) {
            SystemModule::updateOrCreate(['system_type' => $module['system_type']], $module);
        }
    }
}