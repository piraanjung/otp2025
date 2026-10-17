<?php

namespace App\Exports;

use App\Models\Tabwater\TwInvoice;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class P17ReportExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading, ShouldAutoSize
{
    protected $periodId;
    protected $search;
    protected $userId;
    protected $meterNumber;

    public function __construct($periodId, $search, $userId, $meterNumber)
    {
        $this->periodId = $periodId;
        $this->search = $search;
        $this->userId = $userId;
        $this->meterNumber = $meterNumber;
    }

    public function query()
    {
        $query = TwInvoice::with([
            'tw_meter_infos.user',
            'tw_acc_transactions',
            'invoice_period'
        ]);

        if ($this->periodId) {
            $query->where('inv_period_id_fk', $this->periodId);
        }

        if ($this->userId) {
            $query->whereHas('tw_meter_infos', function ($q) {
                $q->where('user_id', $this->userId);
            });
        }

        if ($this->meterNumber) {
            $query->whereHas('tw_meter_infos', function ($q) {
                $q->where('meternumber', 'LIKE', "%{$this->meterNumber}%");
            });
        }

        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('tw_meter_infos.user', function ($u) use ($search) {
                    $u->where(DB::raw("CONCAT(COALESCE(prefix,''), COALESCE(firstname,''), ' ', COALESCE(lastname,''))"), 'LIKE', "%{$search}%")
                      ->orWhere('firstname', 'LIKE', "%{$search}%")
                      ->orWhere('lastname', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('tw_meter_infos', function ($m) use ($search) {
                    $m->where('meternumber', 'LIKE', "%{$search}%");
                });
            });
        }

        return $query;
    }

    // กำหนดหัวตาราง Excel ให้ตรงกับ ป.17
    public function headings(): array
    {
        return [
            'ผู้ใช้ประปาเลขที่',
            'ชื่อ-สกุล',
            'บิลที่',
            'อ่านจาก',
            'อ่านถึง',
            'จำนวนหน่วย',
            'คิดเป็นเงิน',
            'เพิ่มให้เต็มอัตราอย่างต่ำ',
            'ภาษีมูลค่าเพิ่ม',
            'รวมเป็นเงิน',
            'คงค้างยกมาแต่เดือนก่อน',
            'รวมทั้งสิ้น',
            'วันที่ชำระ',
            'หน้าบัญชีเงินสด',
            'จำนวนเงินที่ชำระ',
            'คงค้างยกไปเดือนหน้า'
        ];
    }

    // Map ข้อมูลในแต่ละ Row พร้อมคำนวณยอดยกมา
    public function map($invoice): array
    {
        $meterInfo = $invoice->tw_meter_infos;
        $user = $meterInfo->user ?? null;
        $fullName = $user 
            ? trim(($user->prefix ?? '') . ' ' . $user->firstname . ' ' . $user->lastname)
            : '-';

        // คำนวณยอดยกมาจากงวดก่อนหน้า
        $broughtForward = 0;
        if ($this->periodId) {
            $broughtForward = TwInvoice::where('meter_id_fk', $invoice->meter_id_fk)
                ->where('inv_period_id_fk', '<', $this->periodId)
                ->where('status', '!=', 'paid')
                ->get()
                ->sum(function ($prev) {
                    $paid = $prev->tw_acc_transactions->amount ?? 0;
                    return $prev->totalpaid - $paid;
                });
        }

        $grandTotal = $invoice->totalpaid + $broughtForward;
        $paidAmount = $invoice->tw_acc_transactions->amount ?? 0;
        $carriedForward = $grandTotal - $paidAmount;

        return [
            $meterInfo->meternumber ?? '-',
            $fullName,
            $invoice->id,
            $invoice->lastmeter,
            $invoice->currentmeter,
            $invoice->water_used,
            $invoice->paid,
            $invoice->reserve_meter,
            $invoice->vat,
            $invoice->totalpaid,
            $broughtForward,
            $grandTotal,
            optional($invoice->tw_acc_transactions)->trans_date ? \Carbon\Carbon::parse($invoice->tw_acc_transactions->trans_date)->format('d/m/Y') : '-',
            $invoice->tw_acc_transactions->cashbook_page ?? '-',
            $paidAmount,
            $carriedForward
        ];
    }

    // อ่านข้อมูลทีละ 1,000 รายการ เพื่อไม่ให้ Memory เต็ม
    public function chunkSize(): int
    {
        return 1000;
    }
}
