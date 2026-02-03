<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class InvItemTemplateExport implements WithHeadings, ShouldAutoSize
{
    /**
     * กำหนดหัวข้อคอลัมน์ (Header)
     */
    public function headings(): array
    {
        return [
            'name',             // ชื่อพัสดุ *
            'code',             // รหัส/Barcode
            'category',         // หมวดหมู่ (ใส่เป็นชื่อ Text)
            'unit',             // หน่วยนับ *
            'min_stock',        // แจ้งเตือนขั้นต่ำ
            'is_chemical',      // เป็นสารเคมี? (Yes/No หรือ 1/0)
            'return_required',  // ต้องคืน? (Yes/No หรือ 1/0)
            'cas_number',       // CAS Number (ถ้ามี)
            'msds_link',        // Link เอกสาร MSDS
        ];
    }
}
