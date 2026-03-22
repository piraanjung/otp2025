<?php
namespace App\Imports;

use App\Models\KeptKaya\KpTbankItemsPriceAndPoint; // ใช้ตัวนี้ตัวเดียวตามที่คุณมี
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class KpTbankPriceImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // ตรวจสอบว่ามีข้อมูลสำคัญไหม (item_id และ unit_id) ถ้าไม่มีให้ข้ามแถวนี้ไป
        if (empty($row['item_id']) || empty($row['unit_id'])) {
            return null;
        }

        // บันทึกลงตารางเดียวจบ (Flat Table)
        // เมื่อสั่ง return new ... ตัว Boot Method ใน Model จะทำงานปิดราคาเก่าให้เองอัตโนมัติ
        return new KpTbankItemsPriceAndPoint([
            'kp_items_idfk'     => $row['item_id'],
            'kp_units_idfk'     => $row['unit_id'],
            'org_id_fk'         => Auth::user()->org_id_fk ?? 1, // ป้องกันกรณี null
            'price_from_dealer' => $row['price_from_dealer'] ?? 0,
            'price_for_member'  => $row['price_for_member'] ?? 0,
            'point'             => $row['point'] ?? 0,
            'effective_date'    => !empty($row['effective_date']) ? Carbon::parse($row['effective_date']) : Carbon::now(),
            'status'            => 'active',
            'type'              => 'recycle',
            'deleted'           => '0',
            'recorder_id'       => Auth::id(),
        ]);
    }
}
