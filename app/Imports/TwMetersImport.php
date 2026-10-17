<?php

namespace App\Imports;

use App\Models\Tabwater\TwDiscountType;
use App\Models\Tabwater\TwMerterInfos;
use App\Models\Tabwater\TwMeters;
use App\Models\User;
use App\Models\Tabwater\TwMeterType;
use App\Models\Tabwater\TwPaymentType;
use App\Models\Tabwater\TwZonBlocks;
use App\Models\Tabwater\TwZones;
// use App\Models\TwDiscountType; // Uncomment ถ้ามี
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Carbon\Carbon; // สำหรับจัดการวันที่
use Maatwebsite\Excel\Concerns\WithChunkReading;

class TwMetersImport implements ToModel, WithHeadingRow, WithValidation
// WithBatchInserts, WithChunkReading
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // ค้นหา IDs จากชื่อหรือรหัส (หากมี)
        $userId = null;
        if (isset($row['user_id']) && $row['user_id']) {
            $user = User::where('id', $row['user_id'])->first();
            $userId = $user->id ?? null;
        }

        $meterTypeId = null;
        if (isset($row['meter_type_name']) && $row['meter_type_name']) {
            $meterType = TwMeterType::where('meter_type_name', $row['meter_type_name'])->first();
            $meterTypeId = $meterType->id ?? null;
        }
// dd($row);
        // $undertakeZoneId = null;
        // if (isset($row['undertake_zone_name']) && $row['undertake_zone_name']) {
        //     $zone = TwZones::where('zone_name', $row['undertake_zone_name'])->first();
        //     $undertakeZoneId = $zone->id ?? null;
        // }

        // $undertakeSubzoneId = null;
        // if (isset($row['undertake_zone_block_name']) && $row['undertake_zone_block_name']) {
        //     // หาก zone_block_name ซ้ำกันใน zone_id ต่างกัน อาจต้องใช้ undertake_zone_name ด้วย
        //     $subzone = TwZonBlocks::where('zone_block_name', $row['undertake_zone_block_name'])
        //                               ->when($undertakeZoneId, function ($query, $undertakeZoneId) {
        //                                   return $query->where('zone_id', $undertakeZoneId);
        //                               })
        //                               ->first();
        //     $undertakeSubzoneId = $subzone->id ?? null;
        // }

        $paymentId = null;
        if (isset($row['payment_type_name']) && $row['payment_type_name']) {
            $paymentType = TwPaymentType::where('name', $row['payment_type_name'])->first();
            $paymentId = $paymentType->id ?? null;
        }

        $discountTypeId = null;
        if (isset($row['discount_type_name']) && $row['discount_type_name']) {
            $discountType = TwDiscountType::where('name', $row['discount_type_name'])->first();
            $discountTypeId = $discountType->id ?? null;
        }

        // $recorderId = null;
        // if (isset($row['recorder_username']) && $row['recorder_username']) {
        //     $recorder = User::where('username', $row['recorder_username'])->first();
        //     $recorderId = $recorder->id ?? null;
        // }

        // ตรวจสอบข้อมูลที่จำเป็นขั้นต่ำ
        // if (!isset($row['meter_type_name']) || !isset($row['customer_name'])) {
        //     return null; // ข้ามแถวที่ไม่สมบูรณ์
        // }

        // แปลงวันที่ acceptace_date
        $acceptaceDate = null;
        if (isset($row['acceptace_date']) && $row['acceptace_date']) {
            try {
                // Maatwebsite/Excel มักจะส่งวันที่มาเป็น Serial Number ของ Excel
                if (is_numeric($row['acceptace_date'])) {
                    $acceptaceDate = Carbon::createFromTimestamp(
                        \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($row['acceptace_date'])
                    );
                } else { // หรือเป็น String
                    $acceptaceDate = Carbon::parse($row['acceptace_date']);
                }
            } catch (\Exception $e) {
                // หากแปลงไม่ได้ ให้เป็น null
                $acceptaceDate = null;
            }
        }

        // dd($row);

        return new TwMeters([
            'id'                      => $row['id'],
            'user_id'                 => $row['user_id'],//$userId,
            'factory_no'              => $row['factory_no'] ?? null,
            'initial_reading'         => $row['initial_reading'] ?? 0.00,
            'middle_name'             => $row['middle_name'] ?? null,
            'meter_address'           => $row['meter_address'] ?? null,
            'undertake_zone_id'       => $row['undertake_zone_name'],//$undertakeZoneId,
            'undertake_subzone_id'    => $row['undertake_zone_block_name'],//$undertakeSubzoneId,
            'acceptace_date'          => $acceptaceDate,
            'status'                  => $row['status'] ?? 'active',
            'comment'                 => $row['comment'] ?? null,
            'metertype_id'            => $meterTypeId, // FK
            'current_active_reading'  => $row['current_active_reading'] ?? 0.00,
            'owe_count'               => $row['owe_count'] ?? 0,
            'cutmeter'                => filter_var($row['cutmeter'] ?? false, FILTER_VALIDATE_BOOLEAN), // แปลงเป็น boolean
            'payment_id'              => $paymentId, // FK
            'discounttype_id'         => $discountTypeId, // FK
            'recorder_id'             => $row['recorder_username'],//$recorderId, // FK
        ]);
    }

    /**
     * กำหนดกฎการตรวจสอบข้อมูลสำหรับแต่ละแถวที่นำเข้า
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'id'                      => 'required|integer|unique:tw_meters,id',
            'meter_type_name'         => 'required|string|exists:tw_meter_types,meter_type_name', // ชื่อประเภทมิเตอร์ต้องมีอยู่
            'initial_reading'         => 'nullable|numeric|min:0',
            'current_active_reading'  => 'nullable|numeric|min:0',
            'acceptace_date'          => 'nullable|date',
            'status'                  => 'nullable|in:active,inactive,deleted,cutmeter',
            'owe_count'               => 'nullable|integer|min:0',
            'cutmeter'                => 'nullable|boolean', // 0, 1, true, false, "0", "1"
            'username'                => 'nullable|string|exists:users,username', // user_id
            // 'undertake_zone_name'     => 'nullable|string|exists:tw_zones,zone_name', // undertake_zone_id
            // 'undertake_zone_block_name'  => 'nullable|string', // undertake_subzone_id (validation for existance should be more complex if name is not unique)
            'payment_type_name'       => 'nullable|string|exists:tw_payment_types,name', // payment_id
            'discount_type_name'      => 'nullable|string|exists:tw_discount_types,name', // discounttype_id
            // 'recorder_username'       => 'nullable|string|exists:users,username', // recorder_id
            // ... เพิ่ม rules สำหรับคอลัมน์อื่นๆ ตามความเหมาะสม
        ];
    }

    /**
     * กำหนดข้อความแสดงข้อผิดพลาดแบบกำหนดเอง
     *
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'meter_type_name.exists'      => 'Meter Type ":input" not found.',
            'username.exists'             => 'User (username):input not found.',
            'undertake_zone_name.exists'  => 'Undertake Zone ":input" not found.',
            'payment_type_name.exists'    => 'Payment Type ":input" not found.',
            // ...
        ];
    }

    public function headingRow(): int
    {
        return 1;
    }
    /**
     * กำหนดขนาด Batch สำหรับการ Insert
     *
     * @return int
     */
    // public function batchSize(): int
    // {
    //     return 500;
    // }

    // /**
    //  * กำหนดขนาด Chunk สำหรับการอ่านไฟล์
    //  *
    //  * @return int
    //  */
    // public function chunkSize(): int
    // {
    //     return 500;
    // }
}