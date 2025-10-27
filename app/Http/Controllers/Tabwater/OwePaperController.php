<?php

namespace App\Http\Controllers\Tabwater;

use App\Http\Controllers\Controller;

use App\Models\Admin\Zone;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\FunctionsController;
use App\Http\Controllers\Api\InvoiceController as ApiInvoiceCtrl;
use App\Http\Controllers\Api\OwepaperController as ApiOwepaperController;
use App\Models\Tabwater\TwCutmeter;
use App\Models\Tabwater\TwInvoiceTemp;
use App\Models\Tabwater\TwInvoicePeriod;
use App\Models\Tabwater\TwMeterInfos;
use Illuminate\Support\Facades\Auth;

class OwePaperController extends Controller
{
    public function index(REQUEST $request)
    {

        $zones = Zone::all();
        $apiOwepaper = new ApiOwepaperController;
        $oweInvCountGroupByUserId = $apiOwepaper->oweAndInvoiceCount();

        $invoice_period = TwInvoicePeriod::where('status', 'active')->get()->first();
        $zone_id_selected = 'all';
        $subzone_id_selected = 'all';
        return view('owepaper.index', compact(
            'zones',
            'zone_id_selected',
            'subzone_id_selected',
            'oweInvCountGroupByUserId',
            'invoice_period'
        ));
    }

    function print(REQUEST $request)
    {
        date_default_timezone_set('Asia/Bangkok');
        $from_view = $request->get('from_view');
        $funcCtrl = new FunctionsController();
        $oweArray = [];
        $apiInvoiceCtrl = new ApiInvoiceCtrl();
        foreach ($request->get('meter_id') as $key => $on) {
            if ($on == 'on') {
                //checkว่ามีข้อมูลใน cutmeter table หรือยัง
                $checkInitData = TwCutmeter::where('meter_id_fk', $key)->whereIn('status', ['pending', 'cutmeter'])->get();
                $usermeter_info_owe_count = TwMeterInfos::where('meter_id', $key)->get(['owe_count']);
                $progressDecode = [];
                if (collect($checkInitData)->isEmpty()) {
                    array_push($progressDecode, ['topic' => 'warning_print', 'undertaker' => [Auth::user()->id], 'created_at' => strtotime(date('Y-m-d H:i:s'))]);
                    //ถ้า เป็น null ให้ insert ข้อมูลเข้าไป
                    TwCutmeter::insert([
                        'meter_id_fk' => $key,
                        'owe_count' => $usermeter_info_owe_count[0]->owe_count,
                        'warning_print' => 1,
                        'operate_by' => Auth::id(),
                        'status' => 'pending',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                } else {
                    //update cutmeter table
                    TwCutmeter::where('meter_id_fk', $key)->whereIn('status', ['pending', 'cutmeter'])->update([
                        'warning_print' => $checkInitData[0]->warning_print + 1,
                        'status'        =>   'passed',
                        'updated_at'    => date('Y-m-d H:i:s'),
                    ]);
                    TwCutmeter::insert([
                        'meter_id_fk' => $key,
                        'owe_count' => $usermeter_info_owe_count[0]->owe_count,
                        'warning_print' => 1,
                        'operate_by' => Auth::id(),
                        'status' => $checkInitData[0]->status,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    // array_push($progressDecode, ['topic' => 'warning_print', 'undertaker' => [Auth::user()->id], 'created_at' => strtotime(date('Y-m-d H:i:s'))]);
                    
                }
                //หาการใช้น้ำ 5 เดือนล่าสุด
                $oweByInvoicePeriod = TwInvoiceTemp::where('meter_id_fk', $key)
                    ->whereIn('status', ['owe', 'invoice'])
                    ->with('invoice_period', 'tw_meter_infos')
                    ->orderBy('inv_period_id_fk', 'desc')
                    ->get();
                foreach($oweByInvoicePeriod as $invoice){
                    TwInvoiceTemp::where('id', $invoice->id)->update([
                        'printed_time' => $invoice->printed_time + 1
                    ]);
                }


                $oweSum =  collect($oweByInvoicePeriod)->sum('totalpaid');
                $init = "000000000000000000";
                $meter_id_length = strlen($key);
                $meter_id_str  = substr($init, $meter_id_length) . "" . $key;
                $acc_trans_id_fk_length = strlen($oweByInvoicePeriod[0]->acc_trans_id_fk);
                $acc_trans_id_fk_str  = substr($init, $acc_trans_id_fk_length)."".$oweByInvoicePeriod[0]->acc_trans_id_fk;
                $paidVal   = str_replace([".",","],"",number_format($oweSum,2));
                $qrcodeStr = "|099400035262000\n".$meter_id_str."\n".$acc_trans_id_fk_str."\n".$paidVal;

                array_push($oweArray, [
                    'res' => collect($oweByInvoicePeriod)->reverse()->flatten(),
                     'qrcode' => $qrcodeStr,
                ]);
            }
        }
        return view('owepaper.print', compact('oweArray', 'from_view'));
    }
}
