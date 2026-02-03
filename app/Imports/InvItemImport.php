<?php

namespace App\Imports;

use App\Models\InvCategory;
use App\Models\InvItem;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class InvItemImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Logic: แปลงชื่อหมวดหมู่ใน Excel ให้เป็น ID
        // สมมติใน Excel คอลัมน์ชื่อ 'category' ใส่มาเป็น "วัสดุสำนักงาน"
        $category = InvCategory::where('name', $row['category'])->first();

        return new InvItem([
            'name'          => $row['name'],         // ชื่อพัสดุ
            'code'          => $row['code'],         // รหัส
            'unit'          => $row['unit'],         // หน่วยนับ
            'min_stock'     => $row['min_stock'] ?? 0,

            // ถ้าหา ID หมวดหมู่ไม่เจอ ให้ใส่ null หรือ default id
            'inv_category_id_fk' => $category ? $category->id : null,
            'org_id_fk' => Auth::user()->org_id_fk,

            // แปลงค่า Yes/No หรือ 1/0 จาก Excel
            'is_chemical'   => ($row['is_chemical'] == 'Yes' || $row['is_chemical'] == 1) ? 1 : 0,
            'return_required' => ($row['return_required'] == 'Yes' || $row['return_required'] == 1) ? 1 : 0,
        ]);
    }
}
