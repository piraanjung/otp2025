<?php

namespace Database\Seeders;

use App\Models\IssueType;
use Illuminate\Database\Seeder;

class IssueTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            // 💧 ระบบน้ำประปา (water)
            ['system_type' => 'water', 'name' => 'ท่อแตก / ท่อรั่ว', 'is_active' => true, 'is_suggested' => false],
            ['system_type' => 'water', 'name' => 'น้ำไม่ไหล', 'is_active' => true, 'is_suggested' => false],
            ['system_type' => 'water', 'name' => 'น้ำไหลอ่อน', 'is_active' => true, 'is_suggested' => false],
            ['system_type' => 'water', 'name' => 'น้ำขุ่น / มีกลิ่น / มีตะกอน', 'is_active' => true, 'is_suggested' => false],
            ['system_type' => 'water', 'name' => 'มาตรวัดน้ำชำรุด / เสียหาย', 'is_active' => true, 'is_suggested' => false],

            // ♻️ ธนาคารขยะรีไซเคิล (recycle_trash)
            ['system_type' => 'recycle_trash', 'name' => 'ขอแจ้งนัดรถรับซื้อขยะรีไซเคิล', 'is_active' => true, 'is_suggested' => false],
            ['system_type' => 'recycle_trash', 'name' => 'ปัญหาบัญชีสมาชิก / สมุดคู่ฝาก', 'is_active' => true, 'is_suggested' => false],
            ['system_type' => 'recycle_trash', 'name' => 'ถังขยะรีไซเคิลล้น / ชำรุด', 'is_active' => true, 'is_suggested' => false],

            // 🍃 ธนาคารขยะเศษอาหาร (food_waste)
            ['system_type' => 'food_waste', 'name' => 'ถังหมักเศษอาหารมีกลิ่นเหม็นรุนแรง', 'is_active' => true, 'is_suggested' => false],
            ['system_type' => 'food_waste', 'name' => 'ถังหมักเศษอาหารเต็ม / ขอรับปุ๋ย', 'is_active' => true, 'is_suggested' => false],
            ['system_type' => 'food_waste', 'name' => 'อุปกรณ์ถังหมักชำรุด', 'is_active' => true, 'is_suggested' => false],

            // 💡 งานสาธารณสุข / ไฟฟ้า / บริการทั่วไป (public_health)
            ['system_type' => 'public_health', 'name' => 'ไฟถนน / หลอดไฟสาธารณะดับ', 'is_active' => true, 'is_suggested' => false],
            ['system_type' => 'public_health', 'name' => 'ถังขยะทั่วไปล้น / ไม่ได้รับเก็บ', 'is_active' => true, 'is_suggested' => false],
            ['system_type' => 'public_health', 'name' => 'เหตุรำคาญ / กลิ่นขยะทั่วไป', 'is_active' => true, 'is_suggested' => false],
        ];

        foreach ($data as $item) {
            IssueType::create($item);
        }
    }
}