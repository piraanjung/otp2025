<?php
namespace App\Exports;

use App\Models\KeptKaya\KpTbankItems;
use App\Models\KeptKaya\KpTbankUnits;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PriceTemplateExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        // ดึงขยะทุกลูกมา เพื่อให้ Admin ใส่ราคา
        return KpTbankItems::where('status', 'active')->get();
    }

    public function headings(): array
    {
        return [
            'item_id',
            'item_code',
            'item_name',
            'unit_id',
            'unit_name',
            'price_from_dealer',
            'price_for_member',
            'point',
            'effective_date',
            'end_date (ระบุ YYYY-MM-DD หรือว่างไว้ถ้าไม่มีกำหนด)'
        ];
    }

    public function map($item): array
    {
        // ปกติ 1 ขยะ จะมีหน่วยนับหลัก (Bank Unit)
        return [
            $item->id,
            $item->kp_itemscode,
            $item->kp_itemsname,
            $item->unit_bank_idfk,
            $item->unitBank->unit_name ?? '', // ความสัมพันธ์ใน Model
            0, // ราคาจากร้านค้า (รอเติม)
            0, // ราคาให้สมาชิก (รอเติม)
            0, // คะแนน (รอเติม)
            date('Y-m-d'),
            '',
        ];
    }
}
