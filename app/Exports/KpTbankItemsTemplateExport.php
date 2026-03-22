<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class KpTbankItemsTemplateExport implements FromArray, WithHeadings, WithTitle
{
    public function title(): string
    {
        return 'Template_Import_Items';
    }

    public function headings(): array
    {
        return [
            'kp_itemscode',
            'kp_itemsname',
            'unit_bank_idfk',    // ใส่ ID ของหน่วย (เช่น 1 = กก.)
            'unit_kiosk_idfk',   // ใส่ ID ของหน่วย (เช่น 2 = ขวด)
            'kp_items_group_idfk',
            'ef_id_fk',
            'status',            // active / inactive
            'favorite'           // 1 / 0
        ];
    }

    public function array(): array
    {
        // ใส่ข้อมูลตัวอย่าง (Sample) ให้ผู้ใช้ดูเป็นแนวทาง
        return [
            [
                'PET-001',
                'ขวดน้ำใส (PET)',
                '1',
                '2',
                '1',
                '10',
                'active',
                '1'
            ],
            [
                'ALU-001',
                'กระป๋องอลูมิเนียม',
                '1',
                '', // เว้นว่างได้ถ้าไม่โชว์ที่ตู้
                '2',
                '15',
                'active',
                '0'
            ],
        ];
    }
}
