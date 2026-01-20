<?php

namespace App\Exports;

use App\Models\KeptKaya\WasteBinSubscription; // เช็คชื่อ Model ของคุณให้ถูกต้อง
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KorKor3Export implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

   public function query()
{
    // 1. ดึงค่าจาก Request
    $search = $this->request->input('search');
    $status = $this->request->input('status');
    
    // 2. แก้ไขจุดนี้: ใส่ Logic ค่าเริ่มต้นให้ปีงบประมาณ
    // ถ้าไม่มีค่า fy ส่งมา ให้ใช้ปีปัจจุบัน (เรียก Model หรือคำนวณเอา)
    $fy = $this->request->input('fy');
    
    if (!$fy) {
        // ใช้ Logic เดียวกับ Controller หรือเรียก Static function ที่คุณมี
        // สมมติว่าใน Model มี function calculateFiscalYear()
        $fy = \App\Models\KeptKaya\WasteBinSubscription::calculateFiscalYear(); 
        
        // หรือถ้าไม่มี function นั้น ให้ใช้ date('Y') + 543 (หรือตาม logic ระบบคุณ)
        // $fy = date('Y') + 543; 
    }

    // 3. สร้าง Query
    $query = WasteBinSubscription::query()
        ->with(['wasteBin.user', 'wasteBin.kpUserGroup'])
        ->where('fiscal_year', $fy); // ตอนนี้ $fy จะไม่เป็น null แล้ว

    // --- ส่วนการค้นหา (Search) ---
    if ($search) {
        $query->whereHas('wasteBin', function ($q) use ($search) {
            $q->where('bin_code', 'like', "%{$search}%")
              ->orWhereHas('user', function ($u) use ($search) {
                  $u->where('firstname', 'like', "%{$search}%")
                    ->orWhere('lastname', 'like', "%{$search}%");
              });
        });
    }

    // --- ส่วนกรองสถานะ (Filter Status) ---
    if ($status == 'paid') {
        $query->where('status', 'paid');
    } elseif ($status == 'pending') {
        $query->where('status', '!=', 'paid');
    }

    return $query;
}

    // 2. กำหนดข้อมูลในแต่ละแถว
    public function map($row): array
    {
        $debt = $row->annual_fee - $row->total_paid_amt;
        
        return [
            $row->wasteBin->bin_code, // รหัสถัง
            ($row->wasteBin->user->firstname ?? '-') . ' ' . ($row->wasteBin->user->lastname ?? ''), // ชื่อ-สกุล
            $row->wasteBin->user->address ?? '-', // ที่อยู่
            $row->wasteBin->kpUserGroup->usergroup_name ?? 'ทั่วไป', // ประเภท
            number_format($row->annual_fee, 2), // ยอดประเมิน
            number_format($row->total_paid_amt, 2), // ชำระแล้ว
            number_format($debt, 2), // ค้างชำระ
            ($row->status == 'paid') ? 'ครบถ้วน' : (($row->total_paid_amt > 0) ? 'บางส่วน' : 'ค้างชำระ'), // สถานะ
        ];
    }

    // 3. หัวตาราง
    public function headings(): array
    {
        return [
            'รหัสถัง',
            'ชื่อ-สกุล',
            'ที่อยู่',
            'ประเภทผู้ใช้',
            'ยอดประเมิน',
            'ชำระแล้ว',
            'ค้างชำระ',
            'สถานะ',
        ];
    }

    // 4. จัดรูปแบบตัวหนาที่หัวตาราง
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}