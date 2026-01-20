<?php

namespace App\Http\Controllers\KeptKaya;

use App\Http\Controllers\Controller;
use App\Models\KeptKaya\WasteBinSubscription;
use Illuminate\Http\Request;
use App\Exports\KorKor3Export;
use Maatwebsite\Excel\Facades\Excel;
class KorKor3Controller extends Controller
{
    public function index(Request $request)
    {
        // 1. ตั้งค่าปีงบประมาณ
        $currentFiscalYear = WasteBinSubscription::calculateFiscalYear();
        $fiscalYear = $request->input('fy', $currentFiscalYear); // ค่า Default คือปีปัจจุบัน

        // 2. เตรียม Query หลัก (ดึงข้อมูล Subscription + ถัง + เจ้าของ)
        $query = WasteBinSubscription::query()
            ->with(['wasteBin.user', 'wasteBin.kpUserGroup'])
            ->where('fiscal_year', $fiscalYear);

        // --- ส่วนการค้นหา (Search) ---
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('wasteBin', function($b) use ($search) {
                    $b->where('bin_code', 'like', "%{$search}%") // ค้นหารหัสถัง
                      ->orWhereHas('user', function($u) use ($search) {
                          $u->where('firstname', 'like', "%{$search}%") // ค้นหาชื่อ
                            ->orWhere('lastname', 'like', "%{$search}%");
                      });
                });
            });
        }

        // --- ส่วนกรองสถานะ (Filter Status) ---
        if ($request->filled('status')) {
            if ($request->status == 'paid') {
                $query->where('status', 'paid');
            } elseif ($request->status == 'pending') {
                $query->where('status', '!=', 'paid');
            }
        }

        // 3. คำนวณยอดสรุป (Dashboard Cards) - คำนวณจาก Query ที่ยังไม่ตัดหน้า (Pagination)
        // ใช้ clone เพื่อไม่ให้กระทบ query หลัก
        $summaryQuery = clone $query; 
        
        $totalItems = $summaryQuery->count(); // จำนวนราย
        $totalRevenue = $summaryQuery->sum('annual_fee'); // ยอดเงินประเมินทั้งหมด
        $totalCollected = $summaryQuery->sum('total_paid_amt'); // ยอดที่เก็บได้จริง
        $totalOutstanding = $totalRevenue - $totalCollected; // ยอดค้างชำระ
        
        // เปอร์เซ็นต์การจัดเก็บ
        $collectionProgress = $totalRevenue > 0 ? ($totalCollected / $totalRevenue) * 100 : 0;

        // 4. ดึงข้อมูลเข้าตาราง (Pagination)
        $registries = $query->orderBy('id', 'asc')->paginate(20);

        // เตรียมปีงบประมาณสำหรับ Dropdown
        $availableFiscalYears = range($currentFiscalYear - 2, $currentFiscalYear + 1);

        return view('keptkayas.korkor3.index', compact(
            'registries', 
            'fiscalYear', 
            'availableFiscalYears',
            'totalItems',
            'totalRevenue',
            'totalCollected',
            'totalOutstanding',
            'collectionProgress'
        ));
    }

    public function exportKorKor3(Request $request) 
{
    // ตั้งชื่อไฟล์ตามปีงบประมาณ
    $fileName = 'ทะเบียนคุม_กค3_ปี' . ($request->fy ?? date('Y')) . '.xlsx';
    
    // ส่ง Request (ที่มีค่า filter) ไปให้ Export Class ประมวลผล
    return Excel::download(new KorKor3Export($request), $fileName);
}
}