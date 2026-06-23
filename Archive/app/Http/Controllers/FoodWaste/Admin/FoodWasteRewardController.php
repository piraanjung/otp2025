<?php

namespace App\Http\Controllers\FoodWaste\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FoodWasteRewardController extends Controller
{
    // 1. หน้าแสดงรายการตั้งค่าทั้งหมด
    public function index()
    {
        $settings = DB::table('foodwaste_reward_settings')->get();
        return view('foodwaste.admin.rewards.index', compact('settings'));
    }

    // 2. รับค่าจากฟอร์มมาอัปเดต
    public function update(Request $request)
    {
        // วนลูปอัปเดตตาม Key ที่ส่งมาจากฟอร์ม
        foreach ($request->settings as $key => $value) {
            DB::table('foodwaste_reward_settings')
                ->where('key', $key)
                ->update([
                    'value' => $value,
                    'updated_at' => now()
                ]);
        }

        return back()->with('success', 'บันทึกการตั้งค่าแต้มรางวัลเรียบร้อยแล้ว!');
    }
}
