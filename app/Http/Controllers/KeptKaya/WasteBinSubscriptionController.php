<?php

namespace App\Http\Controllers\KeptKaya;

use App\Http\Controllers\Controller;
use App\Models\Admin\Organization;
use App\Models\KeptKaya\KpUserWastePreference;
use App\Models\KeptKaya\WasteBinSubscription;
use App\Models\KeptKaya\WasteBinPayment;
use App\Models\KeptKaya\WasteBin; // To potentially link from WasteBin details
use App\Models\KeptKaya\WasteBinPayratePerMonth;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // For staff_id
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Log;

class WasteBinSubscriptionController extends Controller
{
    public function index(Request $request)
{
    // 1. รับค่า Filter ต่างๆ
    $currentFiscalYear = WasteBinSubscription::calculateFiscalYear();
    $fiscalYear = $request->input('fy', $currentFiscalYear); // ไม่ต้อง +543 ที่นี่ ถ้าใน DB เก็บเป็น 2569 อยู่แล้ว
    $search = $request->input('search');
    $statusFilter = $request->input('status', 'all'); // all, paid, pending

    // 2. คำนวณ Stats (Card สรุปผล) - นับจาก Subscription ทั้งหมดในปีนั้น
    $statsQuery = WasteBinSubscription::where('fiscal_year', $fiscalYear);
    
    $totalBins = (clone $statsQuery)->count();
    $paidBins = (clone $statsQuery)->where('status', 'paid')->count();
    // นับรวมที่ยังไม่จ่ายหมด (pending + partially_paid)
    $pendingBins = (clone $statsQuery)->whereIn('status', ['pending', 'partially_paid'])->count(); 

    // 3. เริ่ม Query Main Data (รายชื่อผู้ใช้)
    $query = KpUserWastePreference::query()
        ->with(['user', 'wasteBins' => function($q) use ($fiscalYear) {
            // Eager Load เฉพาะที่มี Subscription ในปีที่เลือก
            $q->whereHas('subscriptions', function($subQ) use ($fiscalYear) {
                $subQ->where('fiscal_year', $fiscalYear);
            })->with(['subscriptions' => function($subQ) use ($fiscalYear) {
                $subQ->where('fiscal_year', $fiscalYear);
            }]);
        }])
        ->where('is_annual_collection', true);

    // 3.1 Logic การค้นหา (ชื่อผู้ใช้ หรือ รหัสถัง)
    if ($search) {
        $query->where(function($q) use ($search) {
            // ค้นหาจากชื่อ User
            $q->whereHas('user', function($u) use ($search) {
                $u->where('firstname', 'like', "%{$search}%")
                  ->orWhere('lastname', 'like', "%{$search}%");
            })
            // หรือ ค้นหาจากรหัสถัง
            ->orWhereHas('wasteBins', function($b) use ($search) {
                $b->where('bin_code', 'like', "%{$search}%");
            });
        });
    }

    // 3.2 Logic กรองสถานะ (จ่ายแล้ว / ค้างชำระ)
    if ($statusFilter !== 'all') {
        $query->whereHas('wasteBins.subscriptions', function($q) use ($fiscalYear, $statusFilter) {
            $q->where('fiscal_year', $fiscalYear);
            
            if ($statusFilter == 'paid') {
                $q->where('status', 'paid');
            } else { // pending or partially_paid
                $q->where('status', '!=', 'paid');
            }
        });
    }
    
    // กรองเฉพาะคนที่มี Subscription ในปีนั้นจริงๆ (ป้องกัน User เก่าที่ปีนี้ไม่ได้ต่อสัญญาหลุดมา)
    $query->whereHas('wasteBins.subscriptions', function($q) use ($fiscalYear) {
        $q->where('fiscal_year', $fiscalYear);
    });

    $preferences = $query->paginate(10);

    // 4. เตรียมข้อมูลปีงบประมาณสำหรับ Dropdown (ย้อนหลัง 2 ปี + ปีหน้า 1 ปี)
    $availableFiscalYears = collect(range($currentFiscalYear - 2, $currentFiscalYear + 1));

    return view('keptkayas.annual_payments.index', compact(
        'preferences', 
        'fiscalYear', 
        'availableFiscalYears',
        'totalBins',
        'paidBins',
        'pendingBins',
        'search',
        'statusFilter'
    ));
}

    public function show($id)
    {
        // 1. ดึงข้อมูล Subscription พร้อม User และ Setting
        $wasteBinSubscription = WasteBinSubscription::with([
            'wasteBin.user.wastePreference',
            // 'paymentDetails' // (Optional) ถ้ามี Relation เก็บประวัติการจ่ายรายเดือน ให้ eager load มาด้วย
        ])->findOrFail($id);

        // 2. เช็คว่าถังขยะนี้เปิดบริการรายปีอยู่หรือไม่
        $isBinActiveForAnnualCollection = false;
        if (
            $wasteBinSubscription->wasteBin &&
            $wasteBinSubscription->wasteBin->user &&
            $wasteBinSubscription->wasteBin->user->wastePreference
        ) {

            $isBinActiveForAnnualCollection = $wasteBinSubscription->wasteBin->user->wastePreference->is_annual_collection;
        }

        // 3. กำหนดช่วงเวลาของปีงบประมาณ
        // --- แก้ไขจุดที่ 1: ดึงปีงบประมาณ และแปลงเป็น ค.ศ. ถ้าจำเป็น ---
    $fiscalYearDB = $wasteBinSubscription->fiscal_year; 
    $fiscalYearAD = ($fiscalYearDB > 2400) ? $fiscalYearDB - 543 : $fiscalYearDB;

    // 2. ตั้งต้นวันที่ 1 ต.ค. ของปีก่อนหน้า (จุดเริ่มปีงบ)
    $startOfFiscalYear = Carbon::create($fiscalYearAD - 1, 10, 1); 

    // 3. หาวันที่สมัคร (Pro-rate)
    if ($wasteBinSubscription->created_at) {
        $memberSince = Carbon::parse($wasteBinSubscription->created_at)->startOfMonth();
    } else {
        $memberSince = $startOfFiscalYear->copy();
    }

    $paymentSchedule = [];

    // 4. Loop 12 เดือน
    for ($i = 0; $i < 12; $i++) {
        $currentDate = $startOfFiscalYear->copy()->addMonths($i);
        $month = $currentDate->month;
        $year = $currentDate->year;

        // --- Logic: เช็คว่าเป็นเดือนก่อนสมัครหรือไม่ ---
        // ถ้าเดือนของบิล (currentDate) < เดือนที่สมัคร (memberSince) -> Active = 0
        $isActive = $currentDate->lt($memberSince) ? 0 : 1;

        // --- Logic การจ่ายเงิน (ตัวอย่าง) ---
        // TODO: ตรงนี้คุณต้อง Query จริงจาก DB ว่าจ่ายหรือยัง
        $dueAmount = $wasteBinSubscription->month_fee;
        $paidAmount = 0; 
        $isPaid = false; 

        $paymentSchedule[] = [
            'month_num' => $month,
            'year' => $year,
            'month_name' => $this->getThaiMonth($month),
            'due_amount' => $dueAmount,
            'paid_amount' => $paidAmount,
            'is_paid' => $isPaid,
            'active' => $isActive, // 0 = ก่อนสมัคร, 1 = ปกติ
        ];
    }

    return view('keptkayas.annual_payments.show', compact(
        'wasteBinSubscription', 
        'paymentSchedule', 
        'isBinActiveForAnnualCollection'
    ));
}
    // Helper Function แปลงชื่อเดือนไทย
    private function getThaiMonth($monthNumber)
    {
        $thaiMonths = [
            1 => 'มกราคม',
            2 => 'กุมภาพันธ์',
            3 => 'มีนาคม',
            4 => 'เมษายน',
            5 => 'พฤษภาคม',
            6 => 'มิถุนายน',
            7 => 'กรกฎาคม',
            8 => 'สิงหาคม',
            9 => 'กันยายน',
            10 => 'ตุลาคม',
            11 => 'พฤศจิกายน',
            12 => 'ธันวาคม'
        ];
        return $thaiMonths[$monthNumber] ?? '';
    }

    // public function print(WasteBinSubscription $wasteBinSubscription)
    // {
    //     $wasteBinSubscription->load(['wasteBin.user', 'payments.staff']);

    //     // Check the status of the associated WasteBin
    //     $isBinActiveForAnnualCollection = $wasteBinSubscription->wasteBin->is_active_for_annual_collection ?? false;

    //     // Generate payment schedule for the fiscal year (Oct to Sep)
    //     $paymentSchedule = [];
    //     $startMonth = 10; // October
    //     $endMonth = 9;    // September

    //     // Determine the actual calendar year for the start of the fiscal year
    //     $startCalYear = $wasteBinSubscription->fiscal_year - 1; // Fiscal year 2024 starts in Oct 2023

    //     for ($i = 0; $i < 12; $i++) {
    //         $currentMonthDate = Carbon::createFromDate($startCalYear, $startMonth, 1)->addMonths($i);

    //         $monthNum = $currentMonthDate->month;
    //         $year = $currentMonthDate->year;

    //         $monthName = $currentMonthDate->locale('th')->monthName; // Get Thai month name

    //         $paymentSchedule[] = [
    //             'month_num' => $monthNum,
    //             'year' => $year,
    //             'month_name' => $monthName,
    //             'due_amount' => $wasteBinSubscription->month_fee, // Use month_fee from model
    //             'paid_amount' => $wasteBinSubscription->getAmountPaidForMonth($monthNum, $year),
    //             'is_paid' => $wasteBinSubscription->isMonthPaid($monthNum, $year),
    //         ];
    //     }

    //     // Pass the payment_date from session if redirected from storePayment
    //     $lastPaymentDate = session('last_payment_date');

    //     // return view('keptkayas.annual_payments.show', compact('wasteBinSubscription', 'paymentSchedule', 'lastPaymentDate', 'isBinActiveForAnnualCollection'));
    // }
    public function storePayment(Request $request, WasteBinSubscription $wasteBinSubscription)
    {
        $request->validate([
            'selected_months' => 'required|array|min:1', // Expect an array of selected months
            'selected_months.*' => 'required|string', // Each item should be 'month|year'
            'amount_paid' => 'required|numeric|min:0.01', // This is the total amount paid for all selected months
            'payment_date' => 'required|date',
            'notes' => 'nullable|string|max:255',
        ]);

        $totalAmountFromCheckboxes = 0;
        $processedMonths = []; // To prevent duplicate processing if somehow value is sent twice

        foreach ($request->selected_months as $monthYearString) {
            list($monthNum, $year) = explode('|', $monthYearString);

            $monthNum = (int) $monthNum;
            $year = (int) $year;

            // Ensure we haven't processed this specific month/year combination already in this request
            if (in_array("{$monthNum}-{$year}", $processedMonths)) {
                continue;
            }
            $processedMonths[] = "{$monthNum}-{$year}";

            // Get the due amount for this specific month from the subscription's monthly fee
            $dueAmountForThisMonth = $wasteBinSubscription->month_fee;

            // Check if a payment for this month/year already exists
            $existingPayment = $wasteBinSubscription->payments()
                ->where('pay_mon', $monthNum)
                ->where('pay_yr', $year)
                ->first();

            $amount_paid_temp = $request->amount_paid;
            if ($existingPayment) {
                // If payment exists, update it (e.g., add to amount_paid)
                // We only add if the existing payment is less than the due amount for this month
                $remainingDue =   $dueAmountForThisMonth - $existingPayment->amount_paid;
                if ($remainingDue > 0) {
                    $amountToAddToExisting = min($amount_paid_temp, $remainingDue); // Take min of remaining due or total amount paid in form
                    $existingPayment->update([
                        'amount_paid' => $existingPayment->amount_paid + $amountToAddToExisting,
                        'pay_date' =>  date('Y-m-d'), // Update to latest payment date
                        'notes' => $request->notes,
                        'staff_id' => Auth::id(),
                    ]);
                    // Deduct from the total amount paid in the form, as it's being distributed
                    $amount_paid_temp -= $amountToAddToExisting;
                }
            } else {

                // Otherwise, create a new payment record
                // Assume the user is paying the full monthly fee for the selected month(s)
                $amountToPayForNewMonth = min($amount_paid_temp, $dueAmountForThisMonth);
                WasteBinPayment::create([
                    'wbs_id' => $wasteBinSubscription->id,
                    'pay_mon' => $monthNum,
                    'pay_yr' => $year,
                    'amount_paid' => $amountToPayForNewMonth, // Pay up to the monthly fee
                    'pay_date' => date('Y-m-d'),
                    'notes' => $request->notes,
                    'staff_id' => Auth::id(),
                ]);
                // Deduct from the total amount paid in the form
                $amount_paid_temp -= $amountToPayForNewMonth;
            }
            $totalAmountFromCheckboxes += $dueAmountForThisMonth; // Sum up the due amounts of selected months
        }

        // Validate that the amount_paid from the form matches the sum of selected months' due amounts
        // This is a crucial check to ensure the user pays the correct calculated amount
        if (abs($request->amount_paid_from_js_calc - $totalAmountFromCheckboxes) > 0.01) { // Use a small tolerance for float comparison
            // If the amounts don't match, it indicates tampering or a calculation error
            // You might want to throw a validation exception or log an error
            // For now, we'll just log it.
            Log::error("Payment amount mismatch for subscription {$wasteBinSubscription->id}. Expected: {$totalAmountFromCheckboxes}, Received: {$request->amount_paid_from_js_calc}");
            // Optionally, return an error or throw an exception
            // throw \Illuminate\Validation\ValidationException::withMessages(['amount_paid' => 'จำนวนเงินที่ชำระไม่ตรงกับยอดรวมเดือนที่เลือก']);
        }


        // Update total_paid_amount and status of the subscription
        // Recalculate total_paid_amount from all payments for this subscription
        $wasteBinSubscription->total_paid_amt = $wasteBinSubscription->payments()->sum('amount_paid');
        if ($wasteBinSubscription->total_paid_amt >= $wasteBinSubscription->annual_fee) {
            $wasteBinSubscription->status = 'paid';
        } elseif ($wasteBinSubscription->total_paid_amt > 0) {
            $wasteBinSubscription->status = 'partially_paid';
        } else {
            $wasteBinSubscription->status = 'pending';
        }
        // You might add 'overdue' status logic based on current date vs due dates
        $wasteBinSubscription->save();


        return redirect()->route('keptkayas.annual_payments.printReceipt', $wasteBinSubscription->id)->with('success', 'บันทึกการชำระเงินเรียบร้อยแล้ว!');
    }


    public function createSubscription(Request $request)
    {
        $request->validate([
            'waste_bin_id' => 'required|exists:waste_bins,id',
            'annual_fee' => 'required|numeric|min:0',
            'fiscal_year' => 'required|integer|min:2000|max:2100', // Example year range
        ]);

        DB::transaction(function () use ($request) {
            $annualFee = $request->annual_fee; // <--- ประกาศตัวแปร annualFee ตรงนี้
            $monthlyFee = $annualFee / 12;

            WasteBinSubscription::firstOrCreate(
                [
                    'waste_bin_id' => $request->waste_bin_id,
                    'fiscal_year' => $request->fiscal_year,
                ],
                [
                    'annual_fee' => $annualFee,
                    'monthly_fee' => $monthlyFee,
                    'total_paid_amount' => 0,
                    'status' => 'pending',
                ]
            );
        });

        return redirect()->back()->with('success', 'สร้างการสมัครสมาชิกรายปีเรียบร้อยแล้ว!');
    }

    public function printReceipt(WasteBinSubscription $wasteBinSubscription)
    {
        $paymentDate = Carbon::parse(date('Y-m-d'));

        $payments = $wasteBinSubscription->payments()
            ->whereDate('pay_date', $paymentDate)
            ->get();
        $paidMonthArr = collect($payments)->pluck('pay_mon');


        if ($payments->isEmpty()) {
            return redirect()->back()->with('error', 'ไม่พบรายการชำระเงินสำหรับวันที่นี้.');
        }

        $totalPaidAmount = $payments->sum('amount_paid');
        $staff = $payments->first()->staff;
        $receiptCode = 'RCPT-' . $wasteBinSubscription->id . '-' . $paymentDate->format('Ymd');

        $data = [
            'subscription' => $wasteBinSubscription,
            'payments' => $payments,
            'paymentDate' => date('Y-m-d'), //$paymentDateString,
            'totalPaidAmount' => $totalPaidAmount,
            'staff' => $staff,
            'receiptCode' => $receiptCode,
        ];
        $orgInfos = Organization::getOrgName(Auth::user()->org_id_fk);
        return view('keptkayas.annual_payments.receipt', compact('data', 'paidMonthArr', 'orgInfos'));
    }

    public function invoice()
    {
        $invoices = WasteBinSubscription::with('wasteBin.user')
            ->whereIn('status', ['partially_paid', 'pending'])->get();

        return view('keptkayas.annual_payments.invoice', compact('invoices'));
    }

    public function printSelectedInvoices(Request $request)
    {
        $request->validate([
            'invoice_ids' => 'required|array|min:1',
            'invoice_ids.*' => 'required|exists:kp_waste_bin_subscriptions,id',
        ]);

        $invoices = WasteBinSubscription::with('wasteBin.user')
            ->whereIn('status', ['partially_paid', 'pending'])
            ->get();

        // จัดกลุ่ม Collection ตาม user_id
        $invoicesByUser = $invoices->groupBy(function ($item) {
            return $item->wasteBin->user_id;
        });

        // ตอนนี้ $invoicesByUser จะเป็น Collection ที่มี key เป็น user_id
        // และ value เป็น Collection ของ invoices ที่มี user_id นั้นๆ



        if ($invoices->isEmpty()) {
            return back()->with('error', 'ไม่พบใบแจ้งหนี้ที่เลือก');
        }

        return view('keptkayas.annual_payments.print_invoices', compact('invoicesByUser'));
    }

    public function history(Request $request)
{
    // 1. ดึงข้อมูลองค์กรครั้งเดียว
    $orgInfos = Organization::getOrgName(Auth::user()->org_id_fk);

    // 2. เตรียม Query หลัก
    $query = WasteBinSubscription::query()
        ->where('status', 'paid') // หรือสถานะที่ถือว่าจ่ายเงินแล้ว
        ->with([
            'wasteBin.user.user_zone', // โหลด User และ Zone
            'payments' // โหลด Payment เพื่อมาทำตารางเดือน
        ]);

    // 3. เตรียมข้อมูลสำหรับ Dropdown Search (List รายชื่อทั้งหมดที่มีประวัติ)
    // ใช้ select เฉพาะที่จำเป็นเพื่อความเบา
    $searchOptions = WasteBinSubscription::where('status', 'paid')
        ->with('wasteBin.user:id,firstname,lastname,address,zone_id')
        ->get()
        ->unique('wasteBin.bin_code'); // ป้องกันถังซ้ำ (กรณีมีหลายปี)

    // 4. Logic การค้นหา
    $selectedSubscriptions = collect([]); // ค่าเริ่มต้นเป็น Collection ว่าง

    if ($request->has('bin_code') && !empty($request->bin_code)) {
        // ถ้ามีการค้นหา ให้ Filter ตาม bin_code
        // หมายเหตุ: ใช้ get() เผื่อ 1 ถังมีประวัติหลายปีงบประมาณ
        $selectedSubscriptions = $query->whereHas('wasteBin', function ($q) use ($request) {
            $q->where('bin_code', $request->bin_code);
        })->orderBy('fiscal_year', 'desc')->get();
    }

    return view('keptkayas.annual_payments.history', compact('searchOptions', 'selectedSubscriptions', 'orgInfos'));
}
}
