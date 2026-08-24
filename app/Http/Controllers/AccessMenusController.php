<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\Tabwater\ReportsController;
use App\Models\Admin\BudgetYear;
use App\Models\Admin\Organization;
use App\Models\Admin\Zone;
use App\Models\KeptKaya\KpPurchaseTransaction;
use App\Models\KeptKaya\KpTbankItems;
use App\Models\KeptKaya\KpTbankItemsPriceAndPoint;
use App\Models\KeptKaya\KpUserWastePreference;
use App\Models\Tabwater\TwInvoice;
use App\Models\Tabwater\TwNotifies;
use App\Models\User;
use Carbon\Carbon;
// use App\Models\User; // ไม่ได้ใช้ เอาออกได้
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AccessMenusController extends Controller
{
    public function accessmenu(Request $request)
    {
        $user = User::find(Auth::id());

        $isStaff = $user->hasRole(['Recycle Bank Staff', 'Tabwater Staff', 'Staff']);

        // 2. เช็ค Session ก่อนเลย ว่าเคยถูกจำว่าเป็น mobile แล้วหรือยัง?
        if (Session::get('is_mobile') && $isStaff) {
             return redirect()->route('staff_accessmenu');
        }

        // 3. ถ้ายังไม่มีใน Session ให้เช็คจาก User Agent
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $isMobileDevice = preg_match(
            "/(android|avantgo|blackberry|bolt|boost|cello|hiptop|irengin|mobi|mini|mo(bil|si)|ntellect|palm|pda|phone|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|wap|windows ce|xda|xiino)/i",
            $userAgent
        );
        if ($isStaff && $isMobileDevice) {
            // *** จำค่าลง Session ไว้เลย ***
            Session::put('is_mobile', true);

            return redirect()->route('staff_accessmenu');
        }

        // --- Logic เดิมสำหรับ Desktop ---
        $orgInfos = Organization::getOrgName($user->org_id_fk);
        return view('accessmenu', compact('orgInfos'));
    }
public function dashboard(Request $request)
{
    // --- 1. เตรียม Object และ Controller ที่ต้องใช้ ---
    $apiUserCtrl = new UsersController();
    $reportCtrl = new ReportsController();

    // ดึง org_id จาก User ที่ Login
    $org_id = Auth::user()->org_id_fk;

    // --- 2. ข้อมูลกราฟจำนวนสมาชิกแยกตาม Subzone ---
    $subzones = Zone::getOrgSubzone('array');
    $user_in_subzone_label = collect($subzones)->pluck('subzone_name');

    $user_count = [];
    foreach ($subzones as $subzone) {
        $user_count[] = $apiUserCtrl->users_subzone_count($subzone['id']);
    }

    $user_in_subzone_data = [
        'labels' => $user_in_subzone_label,
        'data' => $user_count,
    ];

    $user_count_sum = collect($user_count)->sum();
    $subzone_count = count($subzones);

    // --- 3. ข้อมูลกราฟปริมาณการใช้น้ำ ---
    $data = $reportCtrl->water_used($request, 'dashboard');
    $water_used_total = isset($data['data']) ? collect($data['data'])->sum() : 0;

    // --- 4. ข้อมูลยอดเงินรวม ---
    $paid_total = TwInvoice::where('status', 'paid')->sum('totalpaid');
    $vat = TwInvoice::where('status', 'paid')->sum('vat');

    // --- 5. ข้อมูลปีงบประมาณ ---
    $budget_obj = BudgetYear::where('status', 'active')->first();
    $current_budgetyear = $budget_obj ?: (object)['budgetyear_name' => '-'];

    // --- 6. ข้อมูลองค์กร ---
    $orgInfos = Organization::getOrgName($org_id);

    // --- 7. ข้อมูลกราฟสถานะการชำระเงิน แยกตาม Zone (แก้ไขใหม่) ---
    $startOfMonth = Carbon::now()->startOfMonth();
    $endOfMonth   = Carbon::now()->endOfMonth();

    $zones = Zone::where('org_id_fk', $org_id)->get();

    // เตรียมตัวแปร Array สำหรับใส่กราฟ
    $zone_labels = [];
    $zone_paid_data = [];
    $zone_unpaid_data = [];

    foreach ($zones as $zone) {
        $queryBase = DB::table('tw_invoice')
            ->join('tw_meter_infos', 'tw_invoice.meter_id_fk', '=', 'tw_meter_infos.meter_id')
            ->where('tw_meter_infos.undertake_zone_id', $zone->id)
            ->whereBetween('tw_invoice.created_at', [$startOfMonth, $endOfMonth]);

        $paid_count = (clone $queryBase)->where('tw_invoice.status', 'paid')->count();
        $unpaid_count = (clone $queryBase)->where('tw_invoice.status', '!=', 'paid')->count();
        $total_zone = $paid_count + $unpaid_count;

        // เก็บข้อมูลทุกโซน (หรือจะกรองเฉพาะที่มีข้อมูลก็ได้ if $total_zone > 0)
        // แต่กราฟแท่งแสดงค่า 0 ได้ ไม่ error ครับ
        if ($total_zone >= 0) {
            $zone_labels[] = $zone->zone_name;
            $zone_paid_data[] = $paid_count;
            $zone_unpaid_data[] = $unpaid_count;
        }
    }

    $zone_chart_data = [
        'labels' => $zone_labels,
        'paid'   => $zone_paid_data,
        'unpaid' => $zone_unpaid_data
    ];

    // --- 8. ส่งค่าไปยัง View ---
    return view('dashboard', compact(
        'data',
        'user_in_subzone_data',
        'water_used_total',
        'paid_total',
        'vat',
        'user_count_sum',
        'subzone_count',
        'current_budgetyear',
        'orgInfos',
        'zone_chart_data' // <--- เปลี่ยนตัวแปรที่ส่งไป
    ));
}

    public function staff_accessmenu()
    {

//         $waste_items = [
//     [
//         'name' => 'เหล็ก',
//         'buy_price' => '5',
//         'sell_price' => '8',
//         'unittype' => 1 //กก
//     ],
//     [
//         'name' => 'สังกะสี',
//         'buy_price' => '2',
//         'sell_price' => '3',
//         'unittype' => 1 //กก

//     ],
//     [
//         'name' => 'พลาสติกรวม',
//         'buy_price' => '2',
//         'sell_price' => '2',
//         'unittype' => 1 //กก

//     ],
//     [
//         'name' => 'พลาสติกแข็ง',
//         'buy_price' => '1',
//         'sell_price' => '4',
//         'unittype' => 1 //กก
        
//     ],
//     [
//         'name' => 'สายยางเขียว',
//         'buy_price' => '0.50',
//         'sell_price' => '1',
//         'unittype' => 1 //กก

//     ],
//     [
//         'name' => 'สายยางขาว',
//         'buy_price' => '1',
//         'sell_price' => '2',
//         'unittype' => 1 //กก

//     ],
//     [
//         'name' => 'รองเท้าบู๊ท',
//         'buy_price' => '3',
//         'sell_price' => '5',
//         'unittype' => 1 //กก

//     ],
//     [
//         'name' => 'ท่อ PVC ฟ้า',
//         'buy_price' => 1 ,
//         'sell_price' => 3,
//         'unittype' => 1 //กก
//     ],
//     [
//         'name' => 'ท่อ PVC เหลือง-เทา',
//         'buy_price' => 0.50,
//         'sell_price' => 1,
//         'unittype' => 1 //กก
//     ],
//     [
//         'name' => 'แผ่น CD',
//         'buy_price' => '1',
//         'sell_price' => '3',
//         'unittype' => 1 //กก
//     ],
//     [
//         'name' => 'ขวดเพทใส',
//         'buy_price' => '5',
//         'sell_price' => '8',
//         'unittype' => 1 //กก
//     ],
//     [
//         'name' => 'ขวดแก้วใส',
//         'buy_price' => '0.50',
//         'sell_price' => '1',
//         'unittype' => 1 //กก
//     ],
//      [
//         'name' => 'ขวดแก้วขุ่น',
//         'buy_price' => '0.50',
//         'sell_price' => '1',
//         'unittype' => 1 //กก
//     ],
//     [
//         'name' => 'ลีโอ',
//         'buy_price' => '6',
//         'sell_price' => '11',
//         'unittype' => 5 //ลัง
//     ],
//     [
//         'name' => 'ช้าง(ลัง)',
//         'buy_price' => '6',
//         'sell_price' => '12',
//         'unittype' => 5 //ลัง
//     ],
//     [
//         'name' => 'เหล้าขาวเล็ก(ลัง)',
//         'buy_price' => '12',
//         'sell_price' => '19',
//         'unittype' => 5 //ลัง
//     ],
//     [
//         'name' => 'เหล้าขาวใหญ่(ลัง)',
//         'buy_price' => '16',
//         'sell_price' => '21',
//         'unittype' => 5 //ลัง
//     ],
//     [
//         'name' => 'น้ำปลา(ขวด)',
//         'buy_price' => '1',
//         'sell_price' => '1',
//         'unittype' => 2 //ขวด
//     ],
//     [
//         'name' => 'น้ำปลา(ลัง)',
//         'buy_price' => '16',
//         'sell_price' => '18',
//         'unittype' => 5 //ลัง
//     ],
//     [
//         'name' => 'กระดาษขาว-ดำ',
//         'buy_price' => '2',
//         'sell_price' => '4',
//         'unittype' => 1 //กก
//     ],
//     [
//         'name' => 'กระดาษแข็ง',
//         'buy_price' => '1.5',
//         'sell_price' => '2.7',
//         'unittype' => 1 //กก
//     ],
//     [
//         'name' => 'กระดาษย่อย',
//         'buy_price' => '1',
//         'sell_price' => '1.5',
//         'unittype' => 1 //กก
//     ],
//     [
//         'name' => 'โลหะกระป๋อง/โลหะบาง',
//         'buy_price' => '20',
//         'sell_price' => '55',
//         'unittype' => 1 //กก
//     ],
//     [
//         'name' => 'โลหะติดเหล็ก',
//         'buy_price' => '8',
//         'sell_price' => '13',
//         'unittype' => 1 //กก
//     ],
//     [
//         'name' => 'โลหะหนา',
//         'buy_price' => '30',
//         'sell_price' => '42',
//         'unittype' => 1 //กก
//     ],
//     [
//         'name' => 'ทองแดงปลอก',
//         'buy_price' => '180',
//         'sell_price' => '235',
//         'unittype' => 1 //กก
//     ],
//     [
//         'name' => 'แบตเตอรี่ เล็ก',
//         'buy_price' => '10',
//         'sell_price' => '18',
//         'unittype' => 1 //กก
//     ],
//      [
//         'name' => 'แบตเตอรี่ ใหญ่ ',
//         'buy_price' => '15',
//         'sell_price' => '20',
//         'unittype' => 1 //กก
//     ],
//      [
//         'name' => 'แบตเตอรี่มอเตอร์ไซค์',
//         'buy_price' => '18',
//         'sell_price' => '20',
//         'unittype' => 1 //กก
//     ],
//      [
//         'name' => 'แบตเตอรี่รถยนต์',
//         'buy_price' => '280',
//         'sell_price' => '300',
//         'unittype' => 1 //กก
//     ],
//     [
//         'name' => 'สายไฟทองแดง',
//         'buy_price' => '15',
//         'sell_price' => '17',
//         'unittype' => 1 //กก
//     ],
//     [
//         'name' => 'โลหะกระทะ',
//         'buy_price' => '15',
//         'sell_price' => '17',
//         'unittype' => 1 //กก
//     ],
//     [
//         'name' => 'พัดลม เล็ก(ตัว)',
//         'buy_price' => '15',
//         'sell_price' => '20',
//         'unittype' => 7 //กก
//     ],
//     [
//         'name' => 'พัดลม ใหญ่(ตัว)',
//         'buy_price' => '20',
//         'sell_price' => '35',
//         'unittype' => 7 //กก
//     ],
//     [
//         'name' => 'เครื่องซักผ้าเล็ก(เครื่อง)',
//         'buy_price' => 150,
//         'sell_price' => 200,
//         'unittype' => 6 //กก
//     ],
//      [
//         'name' => 'เครื่องซักผ้าใหญ่(เครื่อง)',
//         'buy_price' => '200',
//         'sell_price' => '300',
//         'unittype' => 6 //กก
//     ],
//     [
//         'name' => 'ไฮเนเก้น(ลัง)',
//         'buy_price' => '5',
//         'sell_price' => '7',
//         'unittype' => 5 //ลัง
//     ],
//     [
//         'name' => 'ทองแดงสวย',
//         'buy_price' => '120',
//         'sell_price' => '180',
//         'unittype' => 1 //ล
//     ],
//     [
//         'name' => 'ทองแดงช็อต',
//         'buy_price' => '120',
//         'sell_price' => '180',
//         'unittype' => 1 //ลัง
//     ],
//     [
//         'name' => 'นุ่น',
//         'buy_price' => '2',
//         'sell_price' => '4',
//         'unittype' => 1 //ลัง
//     ],
//     [
//         'name' => 'ทองเหลือง',
//         'buy_price' => '60',
//         'sell_price' => '100',
//         'unittype' => 1 //ลัง
//     ],
//     [
//         'name' => 'ทีวีใหญ่ 21 นิ้ว',
//         'buy_price' => '70',
//         'sell_price' => '100',
//         'unittype' => 6 //ลัง
//     ],
//     [
//         'name' => 'ทีวีเล็ก14 นิ้ว',
//         'buy_price' => '25',
//         'sell_price' => '50',
//         'unittype' => 6//ลัง
//     ],
//     [
//         'name' => 'ตู้เย็น',
//         'buy_price' => '250',
//         'sell_price' => '300',
//         'unittype' => 6//ลัง
//     ],
//     [
//         'name' => 'ขวดสกรีน',
//         'buy_price' => '0.50',
//         'sell_price' => '1',
//         'unittype' => 2//ลัง
//     ],
//     [
//         'name' => 'ทองแดงเผา',
//         'buy_price' => '120',
//         'sell_price' => '180',
//         'unittype' => 1//ลัง
//     ],
//     [
//         'name' => 'ขวดเพทใส 1500 ml.',
//         'buy_price' => '0.04',
//         'sell_price' => '0.06',
//         'unittype' => 2//ลัง
//     ],
//     [
//         'name' => 'ขวดเพทใส 350 ml.',
//         'buy_price' => '0.02',
//         'sell_price' => '0.04',
//         'unittype' => 2//ลัง
//     ],
//     [
//         'name' => 'ขวดPET ใส 750 ml.',
//         'buy_price' => '0.04',
//         'sell_price' => '0.06',
//         'unittype' => 2//ลัง
//     ],
//     [
//         'name' => 'ขวดPET ใส 500 ml.',
//         'buy_price' => '0.02',
//         'sell_price' => '0.04',
//         'unittype' => 2//ลัง
//     ],
// ];

// foreach($waste_items as $waste_item){
//     $item = KpTbankItems::where('kp_itemsname', $waste_item['name'])->get('id')->first();
//   if(!$item){
//     return $waste_item['name'];
//   }
//     KpTbankItemsPriceAndPoint::create([
//         'kp_items_idfk' => $item->id,
//         'org_id_fk'=>2,
//         'price_from_dealer' => $waste_item['sell_price'],
//         'price_for_member' => $waste_item['buy_price'],
//         'effective_date' => date('Y-m-d'),
//         'point' => 20,
//         'type' => $waste_item['buy_price'] < 0.09 ? 'tbox' :'tbank',
//         'kp_units_idfk'=>$waste_item['unittype'],
//         'status' => 'active',
//         'deleted' => '0',
//     ]);

// }
// return 'xx';

        // เพิ่มความปลอดภัย: เช็คอีกทีว่าเป็น Staff จริงไหม ถ้าไม่ใช่ให้ดีดออก
        // if (!$user->hasAnyRole(['Recycle Bank Staff', 'Tabwater Staff', 'Staff'])) { // แก้ตาม DB ของคุณ
        //     return redirect()->route('accessmenu'); // หรือ route อื่น
        // }

        $orgInfos = Organization::find(Auth::user()->org_id_fk);
        $allMembers = KpUserWastePreference::with('user')
                ->where('org_id_fk', Auth::user()->org_id_fk)->get();
        $notifies_pending = TwNotifies::where('status', 'pending')->get();
        $notifies_pending_count = TwNotifies::where('status', 'pending')->count();

         $transaction = KpPurchaseTransaction::where('id', 6)
            ->with('userWastePreference.user', 'details.item', 'details.pricePoint.kp_units_info')
            ->get()->first();
        return view('staff_accessmenu', compact('transaction', 'orgInfos', 'notifies_pending', 'allMembers', 'notifies_pending_count'));
    }
}
