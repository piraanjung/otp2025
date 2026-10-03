<?php

namespace App\Http\Controllers\Tabwater; // บรรทัดนี้ต้องเป๊ะ
use App\Http\Controllers\Controller;
use App\Exports\InvoiceInCurrentInvoicePeriodExport;
use App\Http\Controllers\Api\InvoiceController as ApiInvoiceCtrl;
use App\Http\Controllers\FunctionsController;
use App\Models\Admin\Organization;
use App\Models\Tabwater\TwInvoice;
use App\Models\Tabwater\InvoicePeriod;
use App\Models\Tabwater\Setting;
use App\Models\Admin\Subzone;
use App\Models\User;
use App\Models\Tabwater\TwMeterInfos;
use App\Models\Admin\Zone;
use App\Models\Tabwater\TwInvoiceHistory; // หมายเหตุ: เช็คชื่อ Model ว่าสะกด History หรือ Histoty ตามไฟล์จริงนะครับ
use App\Models\Tabwater\TwMeterType;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class InvoiceController extends Controller
{
    public function index($_budgetyearId = '', $_invPeriod = '') 
{
    $invPeriodModel = new InvoicePeriod();
    $current_inv_period = $invPeriodModel->where('status', 'active')->first();
    
    if (!$current_inv_period) {
        return redirect()->route('admin.invoice_period.index')
            ->with(['message' => 'ยังไม่ได้สร้างรอบบิล', 'color' => 'info']);
    }

    $orgId = Auth::user()->organization->id ?? Auth::user()->org_id_fk;

    // 1. ดึง Invoice ของรอบบิลปัจจุบัน และกรองตาม organization ของผู้ใช้ปัจจุบัน
    $invoices = TwInvoice::where('inv_period_id_fk', $current_inv_period->id)
        ->where('org_id_fk', $orgId) // กรองตรงตั๋ว invoice ได้เลยเพราะมีฟิลด์นี้อยู่แล้ว
        ->with([
            'tw_meter_infos',
            'tw_meter_infos.user',
            'tw_meter_infos.undertake_zone',
            'tw_meter_infos.undertake_subzone',
        ])
        ->get();

    // 2. ดึงจำนวนมิเตอร์ทั้งหมดแยกตาม Subzone (เฉพาะที่ active)
    $allMetersCountBySubzone = TwMeterInfos::where('status', 'active')
        ->selectRaw('undertake_subzone_id, count(*) as total')
        ->groupBy('undertake_subzone_id')
        ->pluck('total', 'undertake_subzone_id');

    // 3. จัดกลุ่มข้อมูลตาม Subzone สำหรับแสดงผลหน้าจอ
    $zones = $invoices->filter(function ($item) {
            return $item->tw_meter_infos && $item->tw_meter_infos->undertake_subzone;
        })
        ->groupBy(function ($item) {
            return $item->tw_meter_infos->undertake_subzone_id;
        })
        ->map(function ($group, $subzoneId) use ($allMetersCountBySubzone) {
            $firstItem = $group->first()->tw_meter_infos;
            $byStatus = $group->groupBy('status');
            
            $invoiceItems = $byStatus->get('invoice', collect());
            $paidItems = $byStatus->get('paid', collect());
            
            $invoiceTotalAmount = $invoiceItems->sum('totalpaid');
            $paidTotalAmount = $paidItems->sum('totalpaid');
            $totalMetersInSubzone = $allMetersCountBySubzone[$subzoneId] ?? 0;
            $currentInvoicedCount = $group->count();
            $notYetInvoicedCount = max(0, $totalMetersInSubzone - $currentInvoicedCount);

            return [
                'zone_id' => $firstItem->undertake_subzone->zone_id ?? ($firstItem->undertake_zone_id ?? 0),
                'zone_info' => $firstItem,
                'subzone_name' => $firstItem->undertake_subzone->subzone_name ?? '-',
                'zone_name' => $firstItem->undertake_zone->zone_name ?? '-',
                'members_count' => $group->count(),
                'owe_over3' => $group->where('tw_meter_infos.owe_count', '>=', 3)->count(),
                'initTotalCount' => $byStatus->get('init', collect())->count(),
                'invoiceTotalCount' => $invoiceItems->count(),
                'invoiceTotalAmount' => $invoiceTotalAmount,
                'paidTotalCount' => $paidItems->count(),
                'paidTotalAmount' => $paidTotalAmount,
                'user_notyet_inv_info' => $notYetInvoicedCount,
                'water_used' => $group->sum('water_used'),
                'net_paid' => $group->sum('totalpaid'),
                'reseve_paid' => $group->sum('reserve_meter'),
                'total_paid' => $invoiceTotalAmount + $paidTotalAmount,
            ];
        })
        ->sortBy('zone_id');

    if ($_budgetyearId == 'from_user_api') {
        $requestedSubzones = json_decode($_invPeriod, true) ?? [];
        $resArray = [];
        foreach ($requestedSubzones as $req) {
            $sid = $req['subzone_id'] ?? null;
            if ($sid && isset($zones[$sid])) {
                $z = $zones[$sid];
                $resArray[] = [
                    'zone_id' => $z['zone_id'],
                    'initTotalCount' => $z['initTotalCount'],
                    'invoiceTotalCount' => $z['invoiceTotalCount'],
                    'members_count' => $z['members_count'],
                    'paidTotalCount' => $z['paidTotalCount'],
                    'undertake_subzone_id' => $z['zone_info']->undertake_subzone_id,
                    'subzone_name' => $z['subzone_name'],
                    'zone_name' => $z['zone_name'],
                ];
            }
        }
        return $resArray;
    }

    return view('invoice.index', compact('zones', 'current_inv_period'));
}

    public function paid($id)
    {
        $inv = $this->get_user_invoice($id);
        $invoice = json_decode($inv->getContent());
        return view('invoice.paid', compact('invoice'));
    }

    public function zone_create(Request $request, $subzone_id, $curr_inv_prd, $new_user = 0)
    {
        // 1. ตรวจสอบ Config (ควรเช็คเฉพาะ MeterType ที่ใช้งานจริงใน Zone นี้จะดีกว่า แต่เช็คกว้างๆ แบบเดิมก็ได้ครับ)
        $hasConfig = TwMeterType::whereHas('rateConfigs')->exists();

        if (!$hasConfig) {
            return redirect()->route('meter_types.index')
                ->with('error', 'กรุณาตั้งค่า "ประเภทผู้ใช้น้ำ" และ "อัตราค่าน้ำ" ก่อนเริ่มออกใบแจ้งหนี้');
        }

        $member_not_yet_recorded_present_inv_period = collect([]);
        $invoices = collect([]);

        if ($new_user > 0) {
            // --- กรณีที่ 1: หา Meter ที่ยัง "ไม่มี" ใบแจ้งหนี้ในรอบเดือนนี้ ---

            // ใช้ whereDoesntHave เพื่อดึงเฉพาะคนที่ "ยังไม่มี Invoice ในรอบปัจจุบัน"
            // หรือดึงคนที่สถานะ Active
            $members = TwMeterInfos::where('undertake_subzone_id', $subzone_id)
                ->where('status', 'active')
                ->whereDoesntHave('invoice', function ($query) use ($curr_inv_prd) {
                    // กรองเอาคนที่มี invoice รอบนี้ออกไป (ดึงเฉพาะคนที่ยังไม่มี)
                    $query->where('inv_period_id_fk', $curr_inv_prd);
                })
                ->select('meter_id', 'undertake_subzone_id', 'factory_no', 'submeter_name', 'meternumber', 'user_id', 'metertype_id') // อย่าลืม PK ของตัวเอง (meter_id)
                ->with(['meter_type', 'user'])
                ->get();

            // จัดรูปแบบให้เหมือนเดิมเผื่อ View ต้องการ Structure แบบนี้
            // (แต่จริงๆ ส่ง $members ไปตรงๆ เลยก็ได้ ถ้าแก้ View)
            $member_not_yet_recorded_present_inv_period[] = $members;
        } else {
            // --- กรณีที่ 2: ดึงใบแจ้งหนี้ที่มีอยู่แล้ว (Init) ---

            // ใช้ whereHas เพื่อ Filter ตั้งแต่ Query (แก้ Performance Issue)
            $invoices = TwInvoice::where('inv_period_id_fk', $curr_inv_prd)
                ->where('status', 'init')
                ->whereHas('tw_meter_infos', function ($query) use ($subzone_id) {
                    $query->where('undertake_subzone_id', $subzone_id);
                })
                ->with([
                    'tw_meter_infos' => function ($query) {
                        // ต้อง select PK/FK ให้ครบ: meter_id (PK ของ infos)
                        $query->select('meter_id', 'undertake_subzone_id', 'user_id', 'factory_no', 'submeter_name', 'metertype_id', 'meternumber');
                    },
                    'tw_meter_infos.user',
                    'tw_meter_infos.meter_type:id,meter_type_name', // เลือกเฉพาะ column ที่ใช้
                    'tw_meter_infos.meter_type.rateConfigs',
                    'tw_meter_infos.meter_type.rateConfigs.Ratetiers'
                ])
                ->get();
        }

        $subzone = Subzone::find($subzone_id);

        // นับจำนวนจากตัวแปร $invoices ได้เลย ไม่ต้อง query ใหม่
        $invoice_remain = $invoices->count();

        // หมายเหตุ: View อาจจะต้องปรับการ loop ตัวแปร member_not_yet_recorded เล็กน้อยถ้า Structure เปลี่ยน
        return view('invoice.zone_create', compact('invoices', 'invoice_remain', 'subzone', 'member_not_yet_recorded_present_inv_period'));
    }
    public function store(Request $request)
    {
        // 1. [Best Practice] Timezone ควรตั้งที่ config/app.php
        // แต่ถ้าจำเป็นต้องตั้งตรงนี้ก็ทำได้ครับ
        date_default_timezone_set('Asia/Bangkok');

        // 2. [Security] ควร Validate ข้อมูลก่อนใช้งานเสมอ
        $request->validate([
            'data' => 'required|array',
            'subzone_id' => 'required',
        ]);

        // ดึง Org ID (สมมติว่า user ผูกกับ org)
        $org_id = Auth::user()->org_id_fk; // หรือ session('org_id') ตามระบบคุณ

        // 3. [Logic/Performance] ดึงรอบบิล Active และต้องเป็นของ Org เราเท่านั้น
        // ตัด setConnection ออก เพราะเราจะใช้ org_id_fk
        $inv_period_table = InvoicePeriod::where('status', 'active')
            ->where('org_id_fk', $org_id) // ✅ Filter ตาม Org
            ->first();

        // [Safety] เช็คว่าเจอรอบบิลไหม ถ้าไม่เจอให้ดีดกลับทันที ไม่งั้นบรรทัดล่างจะ Error
        if (!$inv_period_table) {
            return back()->with([
                'message' => 'ไม่พบรอบบิลที่เปิดใช้งาน (Active) กรุณาตรวจสอบการตั้งค่า',
                'color' => 'danger',
            ]);
        }


        // กรองข้อมูล (Logic: ต้องมีการจดเลขมาจริงๆ หรือมีการใช้น้ำ)
        $items = collect($request->get('data'))->filter(function ($val) {
            // เช็คว่ามี key currentmeter และค่าไม่ว่าง
            return isset($val['currentmeter']) && is_numeric($val['currentmeter']) && $val['totalpaid'] > 0;
        });

        // 4. [Performance] เริ่ม Transaction
        // ถ้า Loop พังกลางทาง Database จะ Rollback กลับไปเหมือนเดิม ไม่ให้ข้อมูลแหว่ง
        DB::beginTransaction();

        try {
            foreach ($items as $inv) {
                // [Security/Logic] คำนวณหน่วยน้ำที่หลังบ้านอีกที เพื่อความชัวร์
                $last_meter = floatval($inv['lastmeter']);
                $curr_meter = floatval($inv['currentmeter']);
                $water_used = $curr_meter - $last_meter;

                // [Logic] ป้องกันหน่วยน้ำติดลบ
                if ($water_used < 0) $water_used = 0;

                // ---------------------------------------------------------
                // Update Or Create (ใส่ org_id_fk ไปด้วย)
                // ---------------------------------------------------------

                $invoice = TwInvoice::updateOrCreate(
                    [
                        'meter_id_fk'   => $inv['meter_id'],
                        'inv_period_id_fk' => $inv_period_table->id,
                        'org_id_fk'  => $org_id // *สำคัญ: ใส่ตรงนี้ด้วยถ้า 1 มิเตอร์ย้าย Org ได้ (แต่ปกติใส่แค่ create ก็พอ)
                    ],
                    [
                        'lastmeter'     => $last_meter,
                        'currentmeter'  => $curr_meter,
                        'water_used'    => $water_used,
                        'reserve_meter' => $inv['meter_reserve_price'] ?? 0,
                        'inv_type'      => ($water_used == 0) ? 'r' : 'u',

                        // หมายเหตุ: ค่าเงิน (Paid/Vat) รับจาก Frontend ได้ 
                        // แต่ถ้าจะให้ Secure สุดๆ ควรคำนวณใหม่ที่นี่ด้วยสูตรเดียวกับ JS
                        'paid'          => $inv['paid'] ?? 0,
                        'vat'           => $inv['vat'] ?? 0,
                        'totalpaid'     => $inv['totalpaid'],
                        'status'        => 'invoice',
                        'recorder_id'   => Auth::id(),
                    ]
                );
            }

            // ถ้าทุกอย่างผ่าน ให้ Commit ลง Database จริง
            DB::commit();
        } catch (Exception $e) {
            // ถ้ามี Error ให้ยกเลิกทั้งหมด
            DB::rollBack();

            // Log error ไว้ดู (สำคัญมาก)
            Log::error('Invoice Store Error: ' . $e->getMessage());

            return back()->with([
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(),
                'color' => 'danger',
            ]);
        }

        // ---------------------------------------------------------
        // Redirect
        // ---------------------------------------------------------
        return redirect()->route('invoice.zone_create', [
            'zone_id' => $request->get('subzone_id'),
            'curr_inv_prd' => $inv_period_table->id
        ])->with([
            'message' => 'บันทึกใบแจ้งหนี้เรียบร้อยแล้ว', // แก้คำผิด massage -> message
            'color' => 'success',
        ]);
    }
   
    public function edit($invoice_id)
    {
        $inv = $this->get_user_invoice($invoice_id);
        $invoice = json_decode($inv->getContent());
        return view('invoice.edit', compact('invoice'));
    }

    public function update(Request $request, $id)
    {
        date_default_timezone_set('Asia/Bangkok');
        $invoice = TwInvoice::find($id);
        if ($invoice) {
            $invoice->currentmeter = $request->get('currentmeter');
            $invoice->status = $request->get('status');
            $invoice->recorder_id = Auth::id(); // ควรใช้ Auth id แทน hardcode 5
            $invoice->updated_at = now();
            $invoice->save();
        }
        return redirect('invoice/index');
    }

    public function invoiced_lists($subzone_id)
    {
        return view('invoice.invoiced_lists', compact('subzone_id'));
    }

    public function print_multi_invoice(Request $request)
    {
        $validated = $request->validate([
            'inv_id' => 'required',
        ], [
            'required' => 'ยังไม่ได้เลือกแถวที่ต้องการปริ้น',
        ]);

        date_default_timezone_set('Asia/Bangkok');

        if ($request->get('mode') == 'payment') {
            foreach ($request->get('payments') as $key => $val) {
                TwInvoice::where('meter_id_fk', $key)->update([
                    'status' => 'paid',
                    'updated_at' => now(),
                ]);
            }
        }

        $setting_tambon_infos_json = Setting::where('name', 'tambon_infos')->value('values');
        $setting_tambon_infos = json_decode($setting_tambon_infos_json, true);

        $setting_invoice_expired = Setting::where('name', 'invoice_expired')->value('values');
        $strStartDate = date('Y-m-d');
        $invoice_expired_next30day = date("Y-m-d", strtotime("+" . $setting_invoice_expired . " day", strtotime($strStartDate)));

        $invoiceArray = [];

        // ใช้ logic ภายใน class เดียวกันเพื่อลด overhead
        foreach ($request->get('inv_id') as $key => $on) {
            if ($on == 'on') {
                // ตรงนี้ถ้า Logic ของ ApiInvoiceCtrl ซับซ้อนมาก ให้ใช้ตัวเดิม
                // แต่ถ้าใช้ get_user_invoice ได้ก็ใช้ตัวนี้แทน
                $apiInvoiceCtrl = new ApiInvoiceCtrl();
                $data = json_decode($apiInvoiceCtrl->get_user_invoice_by_invId_and_mode($key, $request->get('mode'))->getContent(), true);
                array_push($invoiceArray, $data);
            }
        }

        $mode = "multipage";
        $subzone_id = $request->get('subzone_id');
        return view('invoice.print', compact('invoiceArray', 'mode', 'subzone_id', 'setting_tambon_infos', 'invoice_expired_next30day'));
    }

    public function search_from_meternumber($meternumber, $zone_id)
    {
        $usermeterInfos = TwMeterInfos::orWhere('meternumber', 'LIKE', '%' . $meternumber . '%')
            ->where('zone_id', $zone_id)
            ->with('user', 'user.user_profile', 'user.usermeter_info.zone')->first();

        if (!$usermeterInfos) {
            return ['tw_meter_infos' => null, 'invoice' => null];
        }

        $invoice = TwInvoice::where('user_id', $usermeterInfos->user_id)
            ->orderBy('id', 'desc')
            ->first();

        return ['tw_meter_infos' => $usermeterInfos, 'invoice' => $invoice];
    }

    public function not_invoiced_lists()
    {
        $invoice = TwInvoice::where('inv_period_id', 1)->pluck('user_id');
        return User::whereNotIn('id', $invoice)
            ->where('user_cat_id', 3)
            ->get();
    }

    public function zone_info($subzone_id)
    {
        $presentInvoicePeriod = InvoicePeriod::where('status', 'active')->first();
        $userMeterInfos = TwMeterInfos::where('undertake_subzone_id', $subzone_id)
            ->where('status', 'active')
            ->with('user_profile', 'zone', 'subzone')
            ->orderBy('undertake_zone_id')->get();

        foreach ($userMeterInfos as $user) {
            $user->invoice = TwInvoice::where('inv_period_id', $presentInvoicePeriod->id)
                ->where('user_id', $user->user_id)->first();
        }

        $totalMemberCount = $userMeterInfos->count();
        $memberNoInvoice = $userMeterInfos->filter(function ($value) {
            return !$value->invoice;
        });
        $memberHasInvoice = $userMeterInfos->filter(function ($value) {
            return $value->invoice;
        })->sortBy('user_id');

        $zoneInfo = $userMeterInfos->first();
        return view('invoice.zone_info', compact('zoneInfo', 'memberHasInvoice', 'memberNoInvoice'));
    }

    public function print_invoice($zone_id, $curr_inv_prd)
    {
        $usermeter_infos = TwMeterInfos::where('undertake_subzone_id', $zone_id)
            ->whereHas('invoice', function ($q) {
                return $q->whereIn('status', ['invoice', 'owe']);
            })
            ->with(['invoice' => function ($q) {
                $q->whereIn('status', ['invoice', 'owe']);
            }])
            ->get(['meter_id', 'user_id']);
        return view('invoice.print_invoice', compact('usermeter_infos'));
    }

    public function invoice_bill_print(Request $request)
    {
        $print_infos = [];
        $currentPeriod = InvoicePeriod::where('status', 'active')->first();
        $a = explode('-', $currentPeriod->inv_p_name);
        $thaiMonthStr = FunctionsController::fullThaiMonth($a[0]);
        $funcCtrl = new FunctionsController();

        foreach ($request->get('a') as $meter_id) {
            $umf = TwMeterInfos::where('id', $meter_id)
                ->with(['invoice' => function ($q) use ($currentPeriod) {
                    // ใช้ current period id แทน hardcode 7
                    return $q->select('*')->where('inv_period_id_fk', $currentPeriod->id);
                }])->first();

            // ข้ามถ้าไม่มีข้อมูล
            if (!$umf || $umf->invoice->isEmpty()) continue;

            $inv_owes = TwInvoice::where('meter_id_fk', $meter_id)
                ->with('invoice_period')
                ->where('status', 'owe')->get();

            $owe_infos = [];
            foreach ($inv_owes as $owe) {
                $dateParts = explode('-', $owe->invoice_period->inv_p_name);
                $oweThaiMonth = FunctionsController::fullThaiMonth($dateParts[0]);
                array_push($owe_infos, [
                    'inv_id' => $owe->inv_id,
                    'inv_period' => $oweThaiMonth . " " . $dateParts[1],
                    'totalpaid' => $owe->totalpaid
                ]);
            }

            $inv_created_at = explode(' ', $umf->invoice[0]->created_at);
            $date = Carbon::parse($inv_created_at[0]);
            $expired_date = $date->addDays(15)->format('Y-m-d');

            $thai_created_date = $funcCtrl->engDateToThaiDateFormat($inv_created_at[0]);
            $thai_expired_date = $funcCtrl->engDateToThaiDateFormat($expired_date);

            array_push($print_infos, [
                'id' => $umf->meter_id,
                'inv_id' =>  $umf->invoice[0]->inv_id,
                'meternumber' => $umf->meternumber,
                'submeter_name' => $umf->submeter_name,
                'user_id' => $umf->user_id,
                'name' => ($umf->user->prefix ?? '') . ($umf->user->firstname ?? '') . " " . ($umf->user->lastname ?? ''),
                'user_address' => ($umf->user->address ?? '') . " " . ($umf->user->user_zone->zone_name ?? ''),
                'lastmeter' => $umf->invoice[0]->lastmeter,
                'currentmeter' => $umf->invoice[0]->currentmeter,
                'water_used' =>  $umf->invoice[0]->water_used,
                'paid' =>  $umf->invoice[0]->paid,
                'vat' =>  $umf->invoice[0]->vat,
                'reservemeter' =>  $umf->invoice[0]->reservemeter,
                'totalpaid' =>  $umf->invoice[0]->totalpaid,
                'period' => $thaiMonthStr . " " . $a[1],
                'created_at' => $thai_created_date,
                'expired_date' => $thai_expired_date,
                'owe_infos' => $owe_infos
            ]);
        }

        $org = Organization::find(2); // หรือดึงจาก Auth
        return view('invoice.print_invoice_bills', compact('org', 'print_infos'));
    }

    public function export_excel(Request $request, $subzone_id, $curr_inv_prd)
    {
        $zone = Zone::where('id', $subzone_id)->first();
        $inv_p = InvoicePeriod::where('id', $curr_inv_prd)->first();

        $text = 'ฟอร์มกรอกข้อมูลเลขมิเตอร์ ' . $zone->zone_name . ' รอบบิลเดือน ' . $inv_p->inv_p_name . '.xlsx';
        return Excel::download(new InvoiceInCurrentInvoicePeriodExport(
            [
                'subzone_id' => $subzone_id,
                'curr_inv_prd' => $curr_inv_prd
            ]
        ), $text);
    }

    public function zone_edit($subzone_id, $curr_inv_prd)
    {
        $userMeterInfos = TwMeterInfos::where('undertake_subzone_id', $subzone_id)
            ->with([
                'invoice_temp' => function ($query) {
                    $query->select(
                        'meter_id_fk',
                        'id',
                        'status',
                        'lastmeter',
                        'currentmeter',
                        'water_used',
                        'paid',
                        'reserve_meter',
                        'vat',
                        'totalpaid',
                        'created_at',
                        'updated_at',
                        'recorder_id',
                    )
                        ->whereIn('status', ['invoice']);
                },
                'meter_type:id,org_id_fk',
                'meter_type.rateConfigs',
                'meter_type.rateConfigs.Ratetiers'
            ])
            ->get(['meter_id', 'undertake_subzone_id', 'user_id', 'meter_address', 'factory_no', 'metertype_id', 'meternumber']);

        if ($userMeterInfos->isEmpty()) {
            return redirect('invoice.index'); // แก้คำผิด invioce
        }

        $inv_in_seleted_subzone = $userMeterInfos->filter(function ($value) {
            return $value->invoice_temp;
        })->values();

        $orgInfos = Organization::getOrgName(Auth::user()->org_id_fk);

        return view('invoice.zone_edit', compact('orgInfos', 'inv_in_seleted_subzone', 'subzone_id'));
    }

    public function reset_invioce_bill($inv_id)
    {
        TwInvoice::where('inv_id', $inv_id)->update([
            'status'        => 'init',
            'currentmeter'  => 0,
            'water_used'    => 0,
            'paid'          => 0,
            'vat'           => 0,
            'totalpaid'     => 0,
        ]);
        return redirect()->back();
    }

    public function zone_update(Request $request)
    {
        date_default_timezone_set('Asia/Bangkok');
        $filters_changeValue = collect($request->get('data'))->filter(function ($val) {
            return $val['changevalue'] == 1;
        });

        if ($filters_changeValue->count() > 0) {
            foreach ($filters_changeValue  as $vals) {
                $invoice = (new TwInvoice())->setConnection(session('db_conn'))->find($vals['inv_id']);

                if ($invoice) {
                    $invoice->currentmeter = $vals['currentmeter'];
                    $invoice->lastmeter    = $vals['lastmeter'];
                    $invoice->paid        = $vals['paid'];
                    $invoice->inv_type    = ($vals['water_used'] == 0) ? 'r' : 'u';
                    $invoice->water_used  = $vals['water_used'];
                    $invoice->reserve_meter = $vals['reserve_meter'];
                    $invoice->vat         = $vals['vat'];
                    $invoice->totalpaid   = $vals['totalpaid'];
                    $invoice->recorder_id  = Auth::id();
                    $invoice->comment      = '';
                    $invoice->updated_at   = now();
                    $invoice->save();
                }
            }
        }

        return redirect('invoice')->with([
            'message' => 'ทำการบันทึกการแก้ไขข้อมูลเรียบร้อยแล้ว',
            'color' => 'success',
        ]);
    }

    public function zone_update2(Request $request)
    {
        date_default_timezone_set('Asia/Bangkok');
        $filters_all = collect($request->get('zone'))->filter(function ($val) {
            return $val['changevalue'] == 1 || $val['status'] == 'delete' || $val['status'] == 'init';
        });

        $filters_changeValue = $filters_all->filter(function ($val) {
            return $val['changevalue'] == 1;
        });

        if ($filters_changeValue->count() > 0) {
            foreach ($filters_changeValue as $key => $vals) {
                TwInvoice::where('id', $key)->update([
                    "currentmeter" => $vals['currentmeter'],
                    "lastmeter" => $vals['lastmeter'],
                    "recorder_id" => Auth::id(),
                    "comment" => $vals['comment'],
                    "updated_at" => now(),
                ]);
            }
        }

        return redirect('invoice/index')->with([
            'massage' => 'ทำการบันทึกการแก้ไขข้อมูลเรียบร้อยแล้ว',
            'color' => 'success',
        ]);
    }

    // --- ส่วนที่ปรับปรุงใหม่ (Refactored Methods) ---

    public function zone_create_for_new_users(Request $request)
    {
        $user_id_array = collect($request->get('new_users'))->flatten();

        $presentInvoicePeriod = InvoicePeriod::where("status", "active")->first();
        $lastInvoicePeriod = InvoicePeriod::where("status", "inactive")->latest('id')->first();

        if (!$lastInvoicePeriod) {
            $lastInvoicePeriod = $presentInvoicePeriod;
        }

        $currentInvPeriod_id = $presentInvoicePeriod->id;
        $subzone_id = $request->get('undertake_subzone_id');
        $new_users = $request->get('new_users');

        foreach ($new_users as $user) {
            TwInvoice::updateOrCreate(
                [
                    'user_id' => $user['user_id'],
                    'inv_period_id' => $currentInvPeriod_id
                ],
                [
                    'meter_id' => $user['user_id'],
                    'lastmeter' => 0,
                    'currentmeter' => 0,
                    'status' => 'init',
                    'deleted' => 0,
                    'recorder_id' => Auth::id(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }

        $member_not_yet_recorded_present_inv_period = TwMeterInfos::where('undertake_subzone_id', $subzone_id)
            ->where('status', 'active')
            ->with([
                'user_profile:name,address,user_id',
                'invoice' => function ($query) use ($currentInvPeriod_id) {
                    return $query->select('inv_period_id', 'currentmeter', 'status', 'id as iv_id', 'user_id')
                        ->where('inv_period_id', '=', $currentInvPeriod_id);
                },
                'invoice_last_inctive_inv_period' => function ($query) use ($currentInvPeriod_id) {
                    return $query->select('inv_period_id', 'currentmeter', 'lastmeter', 'id as iv_id', 'user_id')
                        ->where('inv_period_id', '=', $currentInvPeriod_id);
                },
                'zone:id,zone_name',
                'subzone:id,subzone_name',
            ])
            ->whereIn('user_id', $user_id_array)
            ->orderBy('user_id')
            ->get(['meternumber', 'undertake_zone_id', 'undertake_subzone_id', 'id', 'user_id']);

        return view('invoice.zone_create', compact('member_not_yet_recorded_present_inv_period', 'presentInvoicePeriod'));
    }

    public function delete($invoice_id, $comment)
    {
        TwInvoice::where('id', $invoice_id)->update([
            'status' => 'deleted',
            'deleted' => 1,
            'recorder_id' => Auth::id(),
            'updated_at' => now(),
            'comment' => $comment,
        ]);
        return redirect('invoice/index')->with(['message' => 'ทำการลบข้อมูลเรียบร้อยแล้ว']);
    }

    // --- Private Helper สำหรับ Relation ที่ใช้ซ้ำกัน (ช่วยให้โค้ดสั้นลง) ---
    private function getCommonInvoiceRelations()
    {
        return [
            'tw_meter_infos',
            'tw_meter_infos.user:id,prefix,firstname,lastname,address,zone_id,phone,subzone_id,tambon_code,district_code,province_code',
            'tw_meter_infos.user.user_tambon:id,tambon_name',
            'tw_meter_infos.user.user_district:id,district_name',
            'tw_meter_infos.user.user_province:id,province_name',
            'invoice_period:id,inv_p_name,budgetyear_id',
            'invoice_period.budgetyear:id,budgetyear_name,status',
            'tw_meter_infos.undertake_zone:id,zone_name as undertake_zone_name',
            'tw_meter_infos.undertake_subzone:id,subzone_name as undertake_subzone_name',
            'tw_meter_infos.user.user_zone:id,zone_name as user_zone_name',
            'tw_meter_infos.meter_type:id',
            'tw_meter_infos.meter_type.rateConfigs',
            'tw_meter_infos.meter_type.rateConfigs.Ratetiers'
        ];
    }

    public function get_user_invoice($meter_id, $status = '')
    {
        $query = TwInvoice::where('meter_id_fk', $meter_id)
            ->with($this->getCommonInvoiceRelations())
            ->orderBy('inv_period_id_fk', 'desc');

        if ($status != '') {
            if ($status == 'inv_and_owe') {
                $query->whereIn('status', ['invoice', 'owe']);
            } else {
                $query->where('status', $status);
            }
        }

        $invoices = $query->get([
            'id',
            'meter_id_fk',
            'inv_period_id_fk',
            'reserve_meter',
            'lastmeter',
            'currentmeter',
            'water_used',
            'paid',
            'vat',
            'totalpaid',
            'acc_trans_id_fk',
            'updated_at',
            'status'
        ]);

        return response()->json(collect($invoices)->flatten());
    }

    public function get_invoice_and_invoice_history($meter_id, $status = "")
    {
        // ตรวจสอบ Relation ที่ต้องโหลด
        $relations = $this->getCommonInvoiceRelations();

        // สร้าง Query
        $invoiceQuery = TwInvoice::where('meter_id_fk', $meter_id)->with($relations);
        $historyQuery = TwInvoiceHistory::where('meter_id_fk', $meter_id)->with($relations);

        // กรอง Status
        if ($status != '') {
            $statuses = ($status == 'inv_and_owe') ? ['owe', 'invoice'] : [$status];

            $invoiceQuery->whereIn('status', $statuses);
            $historyQuery->whereIn('status', $statuses);
        }

        // ดึงข้อมูล (ยังไม่ต้อง Order ตรงนี้ เพราะเดี๋ยวต้องเอามารวมกันก่อน)
        $invoices = $invoiceQuery->get();
        $histories = $historyQuery->get();

        // รวมข้อมูล + เรียงลำดับใหม่ (Sort Collection)
        // sortByDesc จะเรียงข้อมูลทั้งหมดที่รวมกันแล้ว ให้เป็นเส้นเวลาเดียวกัน
        $invoiceMerge = $invoices->merge($histories)
            ->sortByDesc('inv_period_id_fk')
            ->values(); // Reset Key ของ Array ให้สวยงาม (0,1,2,...)

        // ส่งคืน JSON (ไม่ต้องใช้ flatten() ก็ได้ถ้า structure เหมือนกัน)
        return response()->json($invoiceMerge);
    }
}
