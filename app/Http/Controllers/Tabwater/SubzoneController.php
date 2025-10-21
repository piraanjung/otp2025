<?php

namespace App\Http\Controllers\Tabwater;

use App\Http\Controllers\Controller;
use App\Models\Admin\ManagesTenantConnection;
use App\Models\Admin\Subzone;
use App\Models\Admin\Zone;
use Illuminate\Http\Request;

class SubzoneController extends Controller
{
    public function edit(Subzone $subzone)
    {
        ManagesTenantConnection::configConnection(session('db_conn'));
        $zone = Zone::where('id', $subzone->id)->with([
            'subzone' => function ($query) {
                return $query->where('status', 'active');
            }
        ])->get();
        return view('admin.subzone.edit', compact('zone'));
    }

    public function update(Request $request, Subzone $subzone) {
        date_default_timezone_set('Asia/Bangkok');
        foreach($request->get('subzone') as $s_zone){
            $index = key($s_zone);

            if(collect( $s_zone[$index]['subzone_name'])->isNotEmpty() && $index == 'new'){
                $new_s_zone = new Subzone();
                $new_s_zone->zone_id        = $subzone->id;
                $new_s_zone->subzone_name   =  $s_zone[$index]['subzone_name'];
                $new_s_zone->created_at     = date('Y-m-d H:i:s');
                $new_s_zone->updated_at     = date('Y-m-d H:i:s');
                $new_s_zone->save();
            }else{
                Subzone::where('id', $index)->update([
                    'subzone_name' => $s_zone[$index]['subzone_name']
                ]);
            }

        }
        return redirect()->back()->with(['message'  => 'ทำการบันทึกการแก้ไขข้อมูลเรียบร้อยแล้ว', 'color' => 'success']);
    }

    public function getSubzone($zone_id) {
        $subzone = Subzone::where("zone_id",$zone_id)->get();
        return response()->json($subzone);
    }
}
