<?php
namespace App\Imports;

use App\Models\KeptKaya\KpTbankItemsPriceAndPoint;
use App\Models\KeptKaya\KpTbankUnits;
use App\Models\KeptKaya\KpTbankItems;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class KpTbankPriceImport implements ToModel
{
    protected $orgId;
    public $successCount = 0; // นับจำนวนที่สำเร็จ

    public function __construct($orgId)
    {
        $this->orgId = $orgId;
    }

    public function model(array $row)
    {
        // 1. ข้ามหัวตาราง (Index 0 ต้องเป็นตัวเลข Item ID)
        if (!isset($row[0]) || !is_numeric($row[0])) {
            return null;
        }

        // 2. ตรวจสอบว่า Item ID นี้มีอยู่ในฐานข้อมูลจริงหรือไม่
        $itemExists = KpTbankItems::where('id', $row[0])
                        ->where('org_id_fk', $this->orgId)
                        ->exists();

        if (!$itemExists) {
            Log::error("Import Error: ไม่พบ Item ID {$row[0]} ในระบบขององค์กรคุณ");
            return null;
        }

        // 3. ค้นหาหน่วยนับ (Index 2) - ใช้ 'unitname' ตามที่คุณแจ้ง
        $unitName = trim($row[2]);
        $unit = KpTbankUnits::where('org_id_fk', $this->orgId)
                    ->where('unitname', $unitName)
                    ->first();

        if (!$unit) {
            Log::error("Import Error: หน่วยนับ '{$unitName}' ไม่มีในระบบ (Item ID: {$row[0]})");
            return null;
        }

        // 4. ถ้าผ่านการเช็คหมดแล้ว ให้ทำการบันทึก
        $itemId = $row[0];
        $unitId = $unit->id;

        // ปิดราคาเก่า
        KpTbankItemsPriceAndPoint::where('kp_items_idfk', $itemId)
            ->where('kp_units_idfk', $unitId)
            ->where('org_id_fk', $this->orgId)
            ->where('status', 'active')
            ->update([
                'status' => 'inactive',
                'end_date' => Carbon::now()
            ]);

        $this->successCount++;

        return new KpTbankItemsPriceAndPoint([
            'kp_items_idfk'     => $itemId,
            'kp_units_idfk'     => $unitId,
            'org_id_fk'         => $this->orgId,
            'price_from_dealer' => $row[3] ?? 0,
            'price_for_member'  => $row[4] ?? 0,
            'point'             => $row[5] ?? 0,
            'type'              => 'tbank',
            'effective_date'    => $this->transformDate($row[7] ?? now()),
            'status'            => 'active',
            'deleted'           => '0',
            'recorder_id'       => Auth::id(),
        ]);
    }

    private function transformDate($value) {
        try {
            if (is_numeric($value)) {
                return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
            }
            return Carbon::parse($value);
        } catch (\Exception $e) {
            return Carbon::now();
        }
    }
}
