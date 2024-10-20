<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InvoicePeriod;
use App\Models\Invoice;
use App\Models\Subzone;
use App\Models\Zone;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubzoneController extends Controller
{
    public function subzone(Request $request)
    {
        $subzones = Subzone::with(['zone' => function ($query) {
            $query->select('id', 'zone_name');
        }]);

        if(!in_array('all',$request->get('zone_id'))){
            $subzones = $subzones->whereIn('zone_id', $request->get('zone_id'));
        }
        //else{
        //     $subzones = $subzones->whereIn('zone_id', '<>', 0);
        // }
        $subzones = $subzones->get()->sortBy('zone_id');
        return response()->json(collect($subzones)->flatten());
    }

    public function getSubzone($subzone_id)
    {
        $subzones = Subzone::where('id', $subzone_id)->get();
        return response()->json($subzones);
    }

    public function get_members_subzone_infos($subzone_id)
    {
        $currentIvPeriod = DB::table('invoice_period as ivp')->where('status', '=', 'active')
            ->select('ivp.id', 'ivp.inv_period_name')->get();
        //ถ้ายังไม่ได้ทำการบันทึกครอบทุกคน  ให้ record_status = 'continue'
        $record_status = 'continue';
        $subzoneQuery = DB::table('subzone as sz')
            ->where('sz.id', '=', $subzone_id)
            ->where('muf.status', '=', 'active')
            ->join('zone as z', 'z.id', '=', 'sz.zone_id')
            ->join('user_meter_infos as muf', 'muf.undertake_subzone_id', '=', 'sz.id');

        //หาชื่อ subzone และ zone
        $subzoneAndZoneName = collect($subzoneQuery->get([
            'z.id as zone_id', 'z.zone_name',
            'sz.id as subzone_id', 'sz.subzone_name',
        ]))->take(1);
        // หาจำนวนสมาชิกทั้งหมด
        $memberCount = $subzoneQuery->count();
        //หา user_id ทั้งหมด
        $allUserId = $subzoneQuery->select('muf.id as meter_id', 'muf.user_id')->get();

        //หา user_id ที่บันทึก Iv แล้ว
        $invUserId = $subzoneQuery->join('invoice as iv', 'iv.meter_id', '=', 'muf.id')
            ->where('inv_period_id', '=', $currentIvPeriod[0]->id)
            ->select('iv.meter_id', 'muf.user_id')->get();
        //หาจำนวนที่ยังไม่ได้บันทึกมิเตอร์รอบปัจจุบัน
        $m = collect($allUserId)->concat($invUserId)->groupBy('meter_id')->values();
        $mm = collect($m)->filter(function ($v) {
            return collect($v)->count() == 1;
        });
        $unRecordMembers = collect([]);
        if (collect($mm)->isNotEMpty()) {
            $memberUnRecordId = collect($mm)->values();
            foreach ($memberUnRecordId as $items) {
                $members = DB::table('subzone as sz')
                    ->join('zone as z', 'z.id', '=', 'sz.zone_id')
                    ->join('user_meter_infos as muf', 'muf.undertake_subzone_id', '=', 'sz.id')->join('user_profile as up', 'up.user_id', '=', 'muf.user_id')
                    ->select(
                        'up.user_id',
                        'up.name',
                        'up.address',
                        'up.zone_id as userprofile_zone_id',
                    )
                    ->where('muf.id', '=', $items[0]->meter_id)
                    ->get();
                $lastIvPeriodInactive = InvoicePeriod::where('status', 'inactive')
                    ->where('deleted', '<>', 1)
                    ->get('id')->last();
                $lastmeter = Invoice::where('inv_period_id', $lastIvPeriodInactive->id)
                    ->where('meter_id', $items[0]->meter_id)
                    ->get('currentmeter');
                $zonename = Zone::where('id', $members[0]->userprofile_zone_id)->get('zone_name');
                $members[0]->lastmeterOfPrvIvPeriod = $lastmeter[0]->currentmeter;
                $members[0]->zone_name = $zonename[0]->zone_name;
                $unRecordMembers->push($members);
            }
        } else {
            //ถ้าทำการบันทึกครอบทุกคนแล้วให้ record_status = 'complete'
            $record_status = 'complete';
        }
        //หาจำนวนที่บันทึกมิเตอร์รอบปัจจุบันแล้ว

        $infos = [
            'inv_period_id' => $currentIvPeriod[0]->id,
            'inv_period_name' => $currentIvPeriod[0]->inv_period_name,
            'zone_name' => $subzoneAndZoneName[0]->zone_name,
            'zone_id' => $subzoneAndZoneName[0]->zone_id,
            'subzone_id' => $subzoneAndZoneName[0]->subzone_id,
            'subzone_name' => $subzoneAndZoneName[0]->subzone_name,
            'member_in_subzone' => $memberCount,
            'record_status' => $record_status,
            'unRecordMembers' => $unRecordMembers,
        ];

        return \response()->json($infos);
    }

    public function get_members_last_inactive_invperiod($subzone_id)
    {
        $presentInvoicePeriod = InvoicePeriod::where('status', 'active')->get()->first();

        $sql = DB::table('user_meter_infos as umf')
            ->join('invoice as iv', 'iv.meter_id_fk', '=', 'umf.meter_id')
            ->join('users as u', 'u.id', '=', 'umf.user_id')
            ->join('zones as z', 'z.id', '=', 'umf.undertake_zone_id')
            ->join('zones as zz', 'zz.id', '=', 'u.zone_id')
            ->join('subzones as sz', 'sz.id', '=', 'umf.undertake_subzone_id')
            ->where('iv.inv_period_id_fk', '=', $presentInvoicePeriod->id)
            ->where('umf.undertake_subzone_id', '=', $subzone_id)
            ->where('umf.status', '=', 'active')
            ->whereIn('iv.status', ['invoice', 'init'])
            ->get([
                'iv.inv_id','iv.lastmeter as init_meter', 'iv.currentmeter',  'iv.inv_period_id_fk', 'iv.status',
                'u.prefix', 'u.firstname', 'u.lastname', 'u.address', 'u.zone_id', 'zz.zone_name as user_zonename',
                'umf.meter_id', 'umf.meternumber', 'umf.user_id',
        ]);

        $presentInvoicePeriodId = $presentInvoicePeriod->id;
        collect($sql)->map(function($item) use($presentInvoicePeriodId){
            $last5InvoiceByInvoicePeriod = Invoice::where('meter_id_fk', $item->meter_id)
            ->whereIn('status', ['owe', 'invoice'])
            ->with(['invoice_period' => function($q){
                return $q->select('id', 'inv_p_name');
            }])
            ->orderBy('inv_period_id_fk', 'desc')
            ->where('inv_period_id_fk', "<>",  $presentInvoicePeriodId)
            ->get(['meter_id_fk', 'inv_period_id_fk','status', 'water_used','totalpaid']);
            $item->owe = $last5InvoiceByInvoicePeriod;
        });


        $invoice_status = collect($sql)->countBy(function ($val) {
            return $val->status;
        });

        return \response()->json(['members' => $sql, 'invoice_status' => $invoice_status]);
    }




    public function delete($id)
    {
        try {
            Subzone::where('id', $id)->delete();
            // FunctionsController::reset_auto_increment_when_deleted('subzones');
            return 1;
        } catch (Exception $e) {
            return 0;
        }
    }
}
