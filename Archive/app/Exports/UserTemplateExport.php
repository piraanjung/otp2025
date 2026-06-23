<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UserTemplateExport implements WithHeadings, ShouldAutoSize, WithStyles
{
    public function headings(): array
    {
        return [
            "org_id_fk",     // รหัสหน่วยงาน
            "username",      // ชื่อผู้ใช้งาน
            "password",      // รหัสผ่าน
            "prefix",        // คำนำหน้า
            "firstname",     // ชื่อจริง
            "lastname",      // นามสกุล
            "email",         // อีเมล
            "id_card",       // เลขบัตรประชาชน
            "line_id",       // LINE ID (ตัวหนังสือ)
            "line_user_id",  // LINE User ID (UID จากระบบ)
            "phone",         // เบอร์โทรศัพท์
            "age",           // อายุ
            "weight",        // น้ำหนัก
            "height",        // ส่วนสูง
            "gender",        // เพศ (male/female)
            "address",       // ที่อยู่/เลขที่บ้าน
            "zone_id",       // รหัสโซน
            "subzone_id",    // รหัสซอย/ชุมชนย่อย
            "tambon_code",   // รหัสตำบล
            "district_code", // รหัสอำเภอ
            "province_code", // รหัสจังหวัด
            "status",        // สถานะ (active/inactive)
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // กำหนดให้แถวที่ 1 (หัวตาราง) เป็นตัวหนา
            1 => ['font' => ['bold' => true]],
        ];
    }
}
