<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmissionFactorExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return ['material_name', 'unit', 'ef_value', 'source', 'example'];
    }

    public function array(): array
    {
        // ข้อมูลตัวอย่างเพื่อให้ User กรอกตามถูก
        return [
            ['พลาสติก PET', 'kgCO2e/kg', '2.15', 'TGO 2025', 'ขวดน้ำใส'],
            ['อลูมิเนียม', 'kgCO2e/kg', '9.12', 'TGO 2025', 'กระป๋องเครื่องดื่ม'],
        ];
    }
}
