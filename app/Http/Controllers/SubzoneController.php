<?php

namespace App\Http\Controllers;

use App\Models\Subzone;
use App\Models\Zone;
use Illuminate\Http\Request;

class SubzoneController extends Controller
{
    public function edit(Subzone $subzone)
    {
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
