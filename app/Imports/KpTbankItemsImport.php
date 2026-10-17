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
       $units = [ 1=>'กิโลกรัม',2=>'ขวด',3=>'ชิ้น',4=>'แผ่น',5=>'ลัง',6=>'เครื่อง',7=>'ตัว',8 =>'ก้อน']  ;
        $group_idfk =
        [ 1=>'เศษเหล็ก',2=>'เศษกระดาษ',3=>'ขวดแก้ว',4=>'พลาสติก',5=>'โลหะที่มีค่าสูง',
            6=>'เครื่องใช้สํานักงานและเครื่องใช้ไฟฟ้า',7=>'อื่นๆ'];
        
        $excelUnit = isset($row['unit_bank_idfk']) ? trim($row['unit_bank_idfk']) : '';
        $excelGroup = isset($row['kp_items_group_idfk']) ? trim($row['kp_items_group_idfk']) : '';

        $unit_bank_idfk = array_search($excelUnit, $units);
        $kp_items_group_idfk = array_search($excelGroup, $group_idfk);
        
        $orgId = Auth::user()->org_id_fk ?? null;

        // ครอบระบบด้วย try-catch เพื่อดักจับ Error
        try {
            return new KpTbankItems([
                'kp_itemscode'        => trim($row['kp_itemscode'] ?? ''), 
                'kp_itemsname'        => trim($row['kp_itemsname'] ?? ''),
                'unit_bank_idfk'      => $unit_bank_idfk !== false ? $unit_bank_idfk : null,
                'unit_kiosk_idfk'     => !empty($row['unit_kiosk_idfk']) ? (int)$row['unit_kiosk_idfk'] : null,
                'kp_items_group_idfk' => $kp_items_group_idfk !== false ? $kp_items_group_idfk : null,
                'ef_id_fk'            => (!empty($row['ef_id_fk']) && $row['ef_id_fk'] != 'null') ? $row['ef_id_fk'] : null,
                'status'              => $row['status'] ?? 'active',
                'favorite'            => $row['favorite'] ?? 0,
                'org_id_fk'           => $orgId,
                'deleted'             => '0',
                'deleted_at'          => null
            ]);
        } catch (\Throwable $e) {
            // ถ้าพังปุ๊บ ให้พ่นข้อมูลของแถวที่มีปัญหา และสาเหตุของ Error ออกมาบนหน้าจอทันที
            dd([
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'row_data_from_excel' => $row, // ดูว่าแถวนี้ใน Excel หน้าตาเป็นยังไง
                'mapped_unit_id' => $unit_bank_idfk,
                'mapped_group_id' => $kp_items_group_idfk,
                'current_org_id' => $orgId
            ]);
        }
    }
															
    // กำหนดกฎการตรวจสอบข้อมูล (Validation)
    public function rules(): array
    {
        return [
            // 'kp_itemscode' => 'required|string',
            // 'kp_itemsname' => 'required|string',
            // 'unit_bank_idfk' => 'required|numeric',
            // 'kp_items_group_idfk' => 'required|numeric',
        ];
    }
}
