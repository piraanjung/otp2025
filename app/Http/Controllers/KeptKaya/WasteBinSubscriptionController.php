<?php

namespace App\Http\Controllers\KeptKaya;

use App\Http\Controllers\Controller;
use App\Models\KeptKaya\WasteBinSubscription;
use App\Models\KeptKaya\WasteBinPayment;
use App\Models\KeptKaya\WasteBin; // To potentially link from WasteBin details
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // For staff_id
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Log;

use function PHPUnit\Framework\returnSelf;

class WasteBinSubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $currentFiscalYear = WasteBinSubscription::calculateFiscalYear();
        $fiscalYear = $request->input('fy', $currentFiscalYear); // Default to current fiscal year

        // NEW: Query users who have subscriptions for the fiscal year
        $users = User::whereHas('wasteBins.subscriptions', function ($q) use ($fiscalYear) {
            $q->where('fiscal_year', $fiscalYear);
        })
        ->with(['wasteBins.subscriptions' => function ($q) use ($fiscalYear) {
            $q->where('fiscal_year', $fiscalYear);
        }])
        ->paginate(10);


        // For fiscal year filter dropdown
        $availableFiscalYears = WasteBinSubscription::select('fiscal_year')
                                ->distinct()
                                ->orderBy('fiscal_year', 'desc')
                                ->pluck('fiscal_year');

        // NEW: Pass users instead of subscriptions to the view
        return view('keptkaya.annual_payments.index', compact('users', 'fiscalYear', 'availableFiscalYears'));
    }
    public function show(WasteBinSubscription $wasteBinSubscription)
    {
        $wasteBinSubscription->load(['wasteBin.user', 'payments.staff']);

        // Check the status of the associated WasteBin
        $isBinActiveForAnnualCollection = $wasteBinSubscription->wasteBin->is_active_for_annual_collection ?? false;

        // Generate payment schedule for the fiscal year (Oct to Sep)
        $paymentSchedule = [];
        $startMonth = 10; // October
        $endMonth = 9;    // September

        // Determine the actual calendar year for the start of the fiscal year
        $startCalYear = $wasteBinSubscription->fiscal_year - 1; // Fiscal year 2024 starts in Oct 2023

        for ($i = 0; $i < 12; $i++) {
            $currentMonthDate = Carbon::createFromDate($startCalYear, $startMonth, 1)->addMonths($i);

            $monthNum = $currentMonthDate->month;
            $year = $currentMonthDate->year;
            
            $monthName = $currentMonthDate->locale('th')->monthName; // Get Thai month name

            $paymentSchedule[] = [
                'month_num' => $monthNum,
                'year' => $year,
                'month_name' => $monthName,
                'due_amount' => $wasteBinSubscription->month_fee, // Use month_fee from model
                'paid_amount' => $wasteBinSubscription->getAmountPaidForMonth($monthNum, $year),
                'is_paid' => $wasteBinSubscription->isMonthPaid($monthNum, $year),
            ];
        }

        // Pass the payment_date from session if redirected from storePayment
        $lastPaymentDate = session('last_payment_date');

        return view('keptkaya.annual_payments.show', compact('wasteBinSubscription', 'paymentSchedule', 'lastPaymentDate', 'isBinActiveForAnnualCollection'));
    }

    public function print(WasteBinSubscription $wasteBinSubscription)
    {
        $wasteBinSubscription->load(['wasteBin.user', 'payments.staff']);

        // Check the status of the associated WasteBin
        $isBinActiveForAnnualCollection = $wasteBinSubscription->wasteBin->is_active_for_annual_collection ?? false;

        // Generate payment schedule for the fiscal year (Oct to Sep)
        $paymentSchedule = [];
        $startMonth = 10; // October
        $endMonth = 9;    // September

        // Determine the actual calendar year for the start of the fiscal year
        $startCalYear = $wasteBinSubscription->fiscal_year - 1; // Fiscal year 2024 starts in Oct 2023

        for ($i = 0; $i < 12; $i++) {
            $currentMonthDate = Carbon::createFromDate($startCalYear, $startMonth, 1)->addMonths($i);

            $monthNum = $currentMonthDate->month;
            $year = $currentMonthDate->year;
            
            $monthName = $currentMonthDate->locale('th')->monthName; // Get Thai month name

            $paymentSchedule[] = [
                'month_num' => $monthNum,
                'year' => $year,
                'month_name' => $monthName,
                'due_amount' => $wasteBinSubscription->month_fee, // Use month_fee from model
                'paid_amount' => $wasteBinSubscription->getAmountPaidForMonth($monthNum, $year),
                'is_paid' => $wasteBinSubscription->isMonthPaid($monthNum, $year),
            ];
        }

        // Pass the payment_date from session if redirected from storePayment
        $lastPaymentDate = session('last_payment_date');

       // return view('keptkaya.annual_payments.show', compact('wasteBinSubscription', 'paymentSchedule', 'lastPaymentDate', 'isBinActiveForAnnualCollection'));
    }
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
                            'payment_date' =>  date('Y-m-d'), // Update to latest payment date
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
                        'payment_date' => date('Y-m-d'),
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
        

        return redirect()->route('keptkaya.annual_payments.printReceipt', $wasteBinSubscription->id)->with('success', 'บันทึกการชำระเงินเรียบร้อยแล้ว!');
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
                                         ->whereDate('payment_date', $paymentDate)
                                         ->get();

        if ($payments->isEmpty()) {
            return redirect()->back()->with('error', 'ไม่พบรายการชำระเงินสำหรับวันที่นี้.');
        }

        $totalPaidAmount = $payments->sum('amt_paid');
        $staff = $payments->first()->staff;
        $receiptCode = 'RCPT-' . $wasteBinSubscription->id . '-' . $paymentDate->format('Ymd');

        $data = [
            'subscription' => $wasteBinSubscription,
            'payments' => $payments,
            'paymentDate' => date('Y-m-d'),//$paymentDateString,
            'totalPaidAmount' => $totalPaidAmount,
            'staff' => $staff,
            'receiptCode' => $receiptCode,
        ];

        return view('keptkaya.annual_payments.receipt',compact('data'));
        // This is the core logic to generate the PDF
        // $pdf = Pdf::loadView('keptkaya.annual_payments.receipt', $data);
        // return $pdf->download('receipt-' . $receiptCode . '.pdf');
    }

      public function invoice(){
         $invoices = WasteBinSubscription::with('wasteBin.user')
            ->whereIn('status', ['partially_paid', 'pending'])->get();
        
        return view('keptkaya.annual_payments.invoice', compact('invoices'));
      }

      public function printSelectedInvoices(Request $request)
    {
        $request->validate([
            'invoice_ids' => 'required|array|min:1',
            'invoice_ids.*' => 'required|exists:waste_bin_subscriptions,id',
        ]);

    $invoices = WasteBinSubscription::with('wasteBin.user')
    ->whereIn('status', ['partially_paid', 'pending'])
    ->get();

// จัดกลุ่ม Collection ตาม user_id
 $invoicesByUser = $invoices->groupBy(function($item) {
    return $item->wasteBin->user_id;
});

// ตอนนี้ $invoicesByUser จะเป็น Collection ที่มี key เป็น user_id
// และ value เป็น Collection ของ invoices ที่มี user_id นั้นๆ

        
       
        if ($invoices->isEmpty()) {
            return back()->with('error', 'ไม่พบใบแจ้งหนี้ที่เลือก');
        }

        return view('keptkaya.annual_payments.print_invoices', compact('invoicesByUser'));
    }
}
