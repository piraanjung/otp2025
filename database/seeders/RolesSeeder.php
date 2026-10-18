<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            // 1. ระดับบริหารและจัดการระบบหลัก (System & Executive)
            'Super Admin',           // ผู้ดูแลระบบสูงสุด (สลับ Org ID ได้)
            'Admin',                 // แอดมินประจำ อปท. (จัดการ User & Permissions ใน อปท.)
            'Executive',             // ผู้บริหาร (นายก/ปลัด/ผอ.) ดู Dashboard & Reports รวมทุกโมดูล

            // 2. ระดับหัวหน้าฝ่าย/ผู้อนุมัติ (Department Headers)
            'Tabwater Header',       // หัวหน้างานประปา
            'Finance Header',        // หัวหน้าการเงิน/คลัง
            'Inventory Header',      // หัวหน้าพัสดุ (อนุมัติใบเบิก)

            // 3. ระดับเจ้าหน้าที่ปฏิบัติงาน (Operational Staffs)
            'Tabwater Staff',        // เจ้าหน้าที่จดมิเตอร์/ออกบิล
            'Finance Staff',         // เจ้าหน้าที่การเงิน/รับชำระ
            'Recycle Bank Staff',    // เจ้าหน้าที่รับซื้อขยะรีไซเคิล
            'Annual Trash Staff',    // เจ้าหน้าที่จัดเก็บค่าถังขยะรายปี
            'Inventory Staff',       // เจ้าหน้าที่คลังพัสดุ
            'Food Waste Staff',      // เจ้าหน้าที่ขยะเปียก/AiroBact
            'Welfare Staff',         // เจ้าหน้าที่กองทุนฌาปนกิจ
            'Saving Bank Staff',     // เจ้าหน้าที่ธนาคารออมทรัพย์

            // 4. ระดับประชาชน/ผู้ใช้งานทั่วไป (End Users / Citizens)
            'User',                  // ประชาชนทั่วไปในระบบ (Base Role)
            'Tabwater User',         // สมาชิกใช้น้ำประปา
            'Recycle Bank User',     // สมาชิกธนาคารขยะรีไซเคิล
            'Annual Trash User',     // สมาชิกผู้ชำระค่าขยะรายปี
            'Food Waste User',       // สมาชิกถังขยะเปียก AiroBact
        ];
        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }
    }
}
