<?php

namespace App\Exports;

use App\Models\Tabwater\TwMeters;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TwMetersExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // โหลดความสัมพันธ์ที่จำเป็นเพื่อดึงชื่อที่เกี่ยวข้อง
        return TwMeters::with([
            'user', // user_id (customer)
            'meterType', // metertype_id
            'undertakeZone', // undertake_zone_id
            'undertakeZoneBlock', // undertake_subzone_id
            'paymentType', // payment_id
            'discountType', // discounttype_id (Uncomment ถ้ามี Model TwDiscountType)
            'recorder' // recorder_id
        ])->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'User ID', // User (customer)
            'Factory No',
            'Initial Reading',
            'Middle Name',
            'Meter Address',
            'Undertake Zone Name',
            'Undertake Zone Block Name',
            'Acceptance Date',
            'Status',
            'Comment',
            'Meter Type Name',
            'Current Active Reading',
            'Owe Count',
            'Cut Meter',
            'Payment Type Name',
            'Discount Type Name',
            'Recorder Username', // Recorder
            'Created At',
            'Updated At',
        ];
    }

    /**
     * @param mixed $meter
     *
     * @return array
     */
    public function map($meter): array
    {
        return [
            $meter->id,
            $meter->user->id ?? null, // User (customer)
            $meter->factory_no,
            $meter->initial_reading,
            $meter->middle_name,
            $meter->meter_address,
            $meter->undertakeZone->zone_name ?? null,
            $meter->undertakeZoneBlock->zone_block_name ?? null,
            $meter->acceptace_date ? $meter->acceptace_date->format('Y-m-d') : null,
            $meter->status,
            $meter->comment,
            $meter->meterType->name ?? null,
            $meter->current_active_reading,
            $meter->owe_count,
            $meter->cutmeter ? 'Yes' : 'No', // แปลง boolean เป็น String
            $meter->paymentType->name ?? null,
            $meter->discountType->name ?? null, // Uncomment ถ้ามี
            $meter->recorder->username ?? null, // Recorder
            $meter->created_at,
            $meter->updated_at,
        ];
    }
}