<?php

namespace App\Imports;

use App\Models\KeptKaya\KpTbankItems;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow; // <--- ต้องมีอันนี้
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Auth;

class KpTbankItemsImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // ดึง org_id_fk จาก User ที่กำลังล็อกอินอยู่
        $orgId = Auth::user()->org_id_fk;

        return new KpTbankItems([
            'kp_itemscode'        => trim($row['kp_itemscode']), // ตัดช่องว่างออก
            'kp_itemsname'        => trim($row['kp_itemsname']),
            'unit_bank_idfk'      => (int)$row['unit_bank_idfk'], // Force เป็นตัวเลข
            'unit_kiosk_idfk'     => !empty($row['unit_kiosk_idfk']) ? (int)$row['unit_kiosk_idfk'] : null,
            'kp_items_group_idfk' => (int)$row['kp_items_group_idfk'],
            'ef_id_fk'            => (!empty($row['ef_id_fk']) && $row['ef_id_fk'] != 'null') ? $row['ef_id_fk'] : null,
            'status'              => $row['status'] ?? 'active',
            'favorite'            => $row['favorite'] ?? 0,
            'org_id_fk'           => $orgId,
            'deleted'             => '0',
        ]);
    }

    // กำหนดกฎการตรวจสอบข้อมูล (Validation)
    public function rules(): array
    {
        return [
            'kp_itemscode' => 'required|string',
            'kp_itemsname' => 'required|string',
            'unit_bank_idfk' => 'required|numeric',
            'kp_items_group_idfk' => 'required|numeric',
        ];
    }
}
