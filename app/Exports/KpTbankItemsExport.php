<?php

namespace App\Exports;

use App\Models\Keptkaya\KpTbankItems;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings; // เพิ่ม WithHeadings สำหรับหัวตาราง

class KpTbankItemsExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // เลือกคอลัมน์ที่คุณต้องการ Export
        return KpTbankItems::select(
            'id',
            'kp_itemscode',
            'kp_itemsname',
            'kp_items_group_idfk',
            'tbank_item_unit_idfk',
            'status',
            'deleted'
        )->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        // กำหนดหัวตารางสำหรับ Excel
        return [
            'ID',
            'Item Code',
            'Item Name',
            'Item Group ID',
            'Item Unit ID',
            'Status',
            'Deleted',
        ];
    }
}