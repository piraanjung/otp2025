<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    public function index()
    {
        $organization_sql = Setting::where('name', 'organization')->get(['values'])->first();
        if (collect($organization_sql)->count() == 0) {
            return view('admin.settings.index');
        }
        $organizations = \json_decode($organization_sql['values'], true);
        $logo = Setting::where('name', 'logo')->get(['values'])->first();
        $meternumber_code = Setting::where('name', 'meternumber_code')->get(['values'])->first();
        $payment_expired_date = Setting::where('name', 'payment_expired_date')->get(['values'])->first();
        $owe_count = Setting::where('name', 'owe_count')->get(['values'])->first();
        $signs_sql = Setting::where('name', 'sign')->get(['values']);
        $signs_arr = collect([]);
        foreach ($signs_sql as $sign) {
            $signs_arr->push(json_decode($sign['values']));
        }
        $signs = collect($signs_arr)->flatten();
        return view(
            'admin.settings.index',
            \compact(
                'logo',
                'signs',
                'organizations',
                'meternumber_code',
                'payment_expired_date',
                'owe_count'
            )
        );
    }

    public function budgetyear()
    {
        return view("admin.settings.budgetyear");
    }

    public function getTambonInfos()
    {
        $settings = Setting::where('name', 'tambon_infos')->get();
        return json_decode($settings[0]['values'], true);
    }

    public function updatebudgetyear(REQUEST $request)
    {
        date_default_timezone_set('Asia/Bangkok');
        $setting = Setting::first();
        $newData = [
            [
                'id' => 1,
                'startyear' => $request['start'],
                'endyear' => $request['end'],
                'create_date' => date('Y-m-d H:i:s'),
                'recorder' => 1
            ]
        ];
        if (is_null($setting)) {
            $newSetting = new Setting;
            $newSetting->budgetyear = \json_encode($newData);
            $newSetting->save();
        } else {
            $lastSetting = json_decode($setting->budgetyear, true);
            $appenddata = [
                'id' => 1,
                'startyear' => $request['start'],
                'endyear' => $request['end'],
                'create_date' => date('Y-m-d H:i:s'),
                'recorder' => 1
            ];
            array_push($lastSetting, $appenddata);
            $setting->budgetyear = \json_encode($lastSetting);
            // $setting->save();
        }

        return \response()->json($newData);
    }

    public function create_and_update(REQUEST $request)
    {
        DB::table('settings')->truncate();
        Setting::create([
            'name' => 'organization',
            'values' => \json_encode([
                "organization_name" => $request->get('organization_name'),
                "organization_short_name" => $request->get('organization_short_name'),
                "organize_address" => $request->get('organize_address'),
                "organize_zone" => $request->get('organize_zone'),
                "organize_road" => $request->get('organize_road'),
                "organize_tambon" => $request->get('organize_tambon'),
                "organize_district" => $request->get('organize_district'),
                "organize_province" => $request->get('organize_province'),
                "organize_zipcode" => $request->get('organize_zipcode'),
                "organize_phone" => $request->get('organize_phone'),
                "department_name" => $request->get('department_name'),
                "department_short_name" => $request->get('department_short_name'),
                "department_phone" => $request->get('department_phone'),
                "organize_email" => $request->get('organize_email'),
            ]),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        Setting::create([
            'name' => 'meternumber_code',
            'values' => $request->get('meternumber_code'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),

        ]);
        Setting::create([
            'name' => 'owe_count',
            'values' => $request->get('owe_count'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),

        ]);
        Setting::create([
            'name' => 'payment_expired_date',
            'values' => $request->get('payment_expired_date'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),

        ]);

        $arr = collect([]);
        //new sign manage
        if ($request->hasFile('new_sign_file')) {
            foreach ($request->file('new_sign_file') as $key => $file) {
                // upload รูปใหม่
                $image_name = 'sign_' . rand() . '.' . $request->file('new_sign_file')[$key]->extension();
                $request->file('new_sign_file')[$key]->move(public_path() . '/sign/', $image_name);
                //ลบรูปเก่า
                // unlink(public_path() . '/sign/'. $item['old_name']);
                $arr->push([
                    'name' => $request->get('new_sign')[$key]['name'],
                    'position' => $request->get('new_sign')[$key]['position'],
                    'image' => $image_name,
                ]);
            }
        }
        // return $request;
        //old sign มีค่า
        if (collect($request->get('old_sign'))->count() > 0) {
            foreach ($request->get('old_sign') as $key => $old) {
                if ($old['change_image'] == 0) {
                    // ไม่มีการเปลี่ยนรูป
                    $image_name = $old['old_name'];
                } else {
                    $image_name = 'sign_' . rand() . '.' . $request->file('old_sign_file')[$key]->extension();
                    $request->file('old_sign_file')[$key]->move(public_path() . '/sign/', $image_name);
                    //ลบรูปเก่า
                    unlink(public_path() . '/sign/' . $old['old_name']);
                }
                $arr->push([
                    'name' => $old['name'],
                    'position' => $old['position'],
                    'image' => $image_name,
                ]);
            }
        }

        Setting::where('id',5)->update([
            'name' => 'sign',
            'values' => json_encode($arr),
        ]);

        if ($request->hasFile('logo')) {
            //เพิ่มหรือเลือกรูป logo ใหม่
            $image_name = time() . '.' . $request->file('logo')->extension();

            Setting::create([
                'name' => 'logo',
                'values' => $image_name,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            if ($request->get('logo_old_image_name')) {
                //ถ้ามีรูปเก่าอยู่ให้ทำการลบรูปจาก folder public logo
                unlink(public_path() . '/logo/' . $request->get('logo_old_image_name'));
            }
            $request->file('logo')->move(public_path() . '/logo/', $image_name);

        } else {
            //ถ้าไม่มีการเพิ่มให้ check ว่ามีข้อมูลชื่อรูปเก่าหรือเปล่า
            if ($request->get('logo_old_image_name')) {
                Setting::create([
                    'name' => 'logo',
                    'values' => $request->get('logo_old_image_name'),
                ]);

            }
        }
        return redirect()->back()->with(['message' => 'ทำการบันทึกข้อมูลแล้ว', 'color' => 'success']);
    }



}
