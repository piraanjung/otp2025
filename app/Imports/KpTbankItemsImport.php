<?php

namespace App\Imports;

use App\Models\KeptKaya\KpTbankItems;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow; // เพิ่ม WithHeadingRow ถ้าไฟล์ Excel มีหัวตาราง

class KpTbankItemsImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // dd($row);
        return new KpTbankItems([
            // 'id' => $row['id'], // ไม่ต้อง Import ID ถ้าเป็น Auto-increment
            'kp_itemscode' => $row['item_code'], // ใช้ชื่อคอลัมน์จาก Excel (แปลงเป็น snake_case หรือตามที่คุณตั้งใน headings)
            'kp_itemsname' => $row['item_name'],
            'kp_items_group_idfk' => $row['item_group_id'],
            'status' => $row['status'],
            'deleted' => '0'//$row['deleted'],
        ]);
    }

    // หากคุณต้องการจัดการกับข้อมูลที่ซ้ำกัน หรือปรับปรุงข้อมูลที่มีอยู่แล้ว
    // คุณอาจจะต้องใช้ WithUpserts หรือ WithBatchInserts/WithChunkReading
    // ดูเอกสาร Maatwebsite/Excel เพิ่มเติม
}
