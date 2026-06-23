<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\KeptKaya\MachineController;
use App\Models\KeptKaya\KpTbankItems;
use App\Models\KeptKaya\Machine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache; // Import Cache
use Illuminate\Support\Facades\Auth;
class DeviceController extends Controller
{
    // Key สำหรับเก็บสถานะเซนเซอร์จำลอง
    const SENSOR_STATUS_KEY = 'object_status';
    // Key สำหรับเก็บคำสั่งควบคุมจำลอง (Reject)
    const CONTROL_SIGNAL_KEY = 'control_signal';

    /**
     * [Web Polling] Web Browser เรียก GET เพื่อดูสถานะเซนเซอร์
     */
    public function getSensorStatus()
    {
        // อ่านค่า has_new_object จาก Cache
        // $status = Cache::get(self::SENSOR_STATUS_KEY, 0); // Default เป็น 0
        $machine =Machine::where('current_user_active_id', 1)->get()->first();
        $status = $machine->has_new_object ?? 0;
        // if(collect($machine)->isNotEmpty()){
        //     Machine::where('current_user_active_id', Auth::id())->update([
        //         'has_new_object' => 1,
        //         'status'=> 'object_detected'
        //     ]);
        //     $status = 1;
        // }
        

        return response()->json(['has_new_object' => (int)$status, 'machine_ready' => $machine->machine_ready]);
    }

    /**
     * [Web Send] Web Browser ส่ง POST เมื่อจำแนกประเภทเสร็จสิ้น
     */
    public function receiveControlSignal(Request $request)
    {
        // Web จะส่ง { reject: 1 } หรือ { reject: 0 }
        $reject = $request->input('reject', 0);
        
        // บันทึกคำสั่งควบคุมลง Cache เพื่อให้ "ESP จำลอง" อ่านไปใช้
        Cache::put(self::CONTROL_SIGNAL_KEY, $reject, 60); // เก็บไว้ 60 วินาที
        
        // **สำคัญ:** เมื่อ Web ทำการตัดสินใจแล้ว (ไม่ว่า Accept หรือ Reject)
        // ต้องสั่ง Reset ค่าสถานะเซนเซอร์กลับไปเป็น 0 ทันที เพื่อให้ Web หยุด Polling
        Cache::put(self::SENSOR_STATUS_KEY, 0, 60); 

        return response()->json([
            'message' => 'Control signal received and sensor status reset.',
            'reject_value' => $reject
        ]);
    }
    
    // ⭐️ NEW: API สำหรับคุณใช้จำลองการ Trigger จาก ESP (POSTMAN/Browser)
    public function updateSensorStatus(Request $request)
    {
        $status = $request->get('has_new_object');
        
        Machine::where('machine_id', $request->get('machine_id'))->update([
            'has_new_object' => $status,
            'updated_at' => Now()
        ]);
        return response()->json([
            'message' => 'Sensor status simulated successfully.',
            'new_status' => (int)$status
        ]);
    }
    
    /**
     * [ESP Polling] ESP8266 เรียก GET เพื่ออ่านคำสั่งควบคุม (Reject)
     */
    public function getControlSignal()
    {
        // อ่านค่าคำสั่งควบคุม (Reject) จาก Cache
        $reject = Cache::get(self::CONTROL_SIGNAL_KEY, 0); 
        
        return response()->json(['reject' => (int)$reject]);
    }

    public function configPricePoints(){
        $items = KpTbankItems::where('kp_items_group_idfk', 5)
            ->with('items_price_and_point_infos', 'items_price_and_point_infos.kp_units_info')
            ->get();

        $arr = [];
        foreach($items as $item){
            array_push($arr,[
               'kp_tbank_item_id'=> $item->id,
               'kp_itemscode' => $item->kp_itemscode,
               'kp_itemscode_short' => str_replace('btmc_', '',$item->kp_itemscode),
                'unit_name'=> $item->items_price_and_point_infos[0]->kp_units_info->unitname, 
                'kp_tbank_items_pricepoint_id'=> $item->items_price_and_point_infos[0]->id,
                'price_per_unit'=> $item->items_price_and_point_infos[0]->price_for_member,
                'point_per_unit'=> $item->items_price_and_point_infos[0]->point,
                
            ]);
        }

         return response()->json($arr, 200);
    }

   
}