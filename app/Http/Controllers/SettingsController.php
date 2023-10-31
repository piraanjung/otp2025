<?php

namespace App\Http\Controllers;

use App\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    public function index()
    {
        $organization_sql = Settings::where('name', 'organization')->get(['values'])->first();
        if (collect($organization_sql)->count() == 0) {
            return view('settings.index');
        }
        $organizations = \json_decode($organization_sql['values'], true);
        $logo = Settings::where('name', 'logo')->get(['values'])->first();
        $meternumber_code = Settings::where('name', 'meternumber_code')->get(['values'])->first();
        $payment_expired_date = Settings::where('name', 'payment_expired_date')->get(['values'])->first();
        $owe_count = Settings::where('name', 'owe_count')->get(['values'])->first();
        $signs_sql = Settings::where('name', 'sign')->get(['values']);
        $signs = collect([]);
        foreach ($signs_sql as $sign) {
            $signs->push(json_decode($sign['values']));
        }
        // return $signs;
        return view('settings.index', \compact('logo', 'signs', 'organizations',
            'meternumber_code', 'payment_expired_date', 'owe_count'));
    }

    public function budgetyear()
    {
        return view("settings.budgetyear");
    }

    public function getTambonInfos()
    {
        $settings = Settings::where('name', 'tambon_infos')->get();
        return json_decode($settings[0]['values'], true);
    }

    public function updatebudgetyear(REQUEST $request)
    {
        date_default_timezone_set('Asia/Bangkok');
        $setting = Settings::first();
        $newData = [['id' => 1, 'startyear' => $request['start'],
            'endyear' => $request['end'], 'create_date' => date('Y-m-d H:i:s'),
            'recorder' => 1]];
        if (is_null($setting)) {
            $newSetting = new Settings;
            $newSetting->budgetyear = \json_encode($newData);
            $newSetting->save();
        } else {
            $lastSetting = json_decode($setting->budgetyear, true);
            $appenddata = ['id' => 1, 'startyear' => $request['start'],
                'endyear' => $request['end'], 'create_date' => date('Y-m-d H:i:s'),
                'recorder' => 1];
            array_push($lastSetting, $appenddata);
            $setting->budgetyear = \json_encode($lastSetting);
            // $setting->save();
        }

        return \response()->json($newData);
    }

    public function create_and_update(REQUEST $request)
    {
        $this->validate($request, [
            // // 'filenames' => 'required',
            // 'filenames.*' => 'mimes:png,jpg,jpeg|max:1014',
            // 'logo.*' => 'mimes:png,jpg,jpeg|max:1014',
            // 'logo' => 'required',
            // "organization_name" => 'required',
            // "department_name" => 'required',
            // "organize_address" => 'required',
            // "organize_zone" => 'required',
            // "organize_road" => 'required',
            // "organize_tambon" => 'required',
            // "organize_district" => 'required',
            // "organize_province" => 'required',
            // "organize_zipcode" => 'required',
            // "organize_phone" => 'required',
            // "organize_email" => 'required',
            // "meternumber_code" => 'required',
            // "owe_count" => 'required',
            // "payment_expired_date" => 'required',

        ]);

        DB::table('settings')->truncate();
        Settings::create([
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
        Settings::create([
            'name' => 'meternumber_code',
            'values' => $request->get('meternumber_code'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),

        ]);
        Settings::create([
            'name' => 'owe_count',
            'values' => $request->get('owe_count'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),

        ]);
        Settings::create([
            'name' => 'payment_expired_date',
            'values' => $request->get('payment_expired_date'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),

        ]);

        $array = collect([]);
        if (collect($request->get('img_name'))->count() > 0) {
            foreach ($request->get('img_name') as $key => $item) {
                if ($item['change_image'] == 0 ) {
                    // ไม่มีการเปลี่ยนรูป
                    $image_name = $item;
                } else {
                    //มีการเปลี่ยนรูป
                    if ($request->file('filenames')[$key]) {
                        // upload รูปใหม่
                        $image_name = 'sign_' . rand() . '.' . $request->file('filenames')[$key]->extension();
                        $request->file('filenames')[$key]->move(public_path() . '/sign/', $image_name);
                        //ลบรูปเก่า
                        unlink(public_path() . '/sign/'. $item['old_name']);
                    }
                }
                $array->push([
                    'name' => $request->get('sign')[$key]['name'],
                    'position' => $request->get('sign')[$key]['position'],
                    'image' => $image_name,
                ]);
            }
            Settings::create([
                'name' => 'sign',
                'values' => json_encode($array),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        
        }
        if ($request->hasFile('logo')) {
            return $request;
            //เพิ่มรูปใหม่
            $image_name = time() . '.' . $request->file('logo')->extension();

            Settings::create([
                'name' => 'logo',
                'values' => $image_name,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $request->file('logo')->move(public_path() . '/logo/', $image_name);

        }
        return redirect('settings');

    }

    public function edit()
    {
        $organization_sql = Settings::where('name', 'organization')->get(['values'])->first();
        $organizations = \json_decode($organization_sql['values'], true);
        // return ;
        $logo = Settings::where('name', 'logo')->get(['values'])->first();
        $logo_values = collect($logo)->count() == 0 ? 0 : \json_decode($logo->values, true);
        $meternumber_code = Settings::where('name', 'meternumber_code')->get(['values'])->first();
        $payment_expired_date = Settings::where('name', 'payment_expired_date')->get(['values'])->first();
        $owe_count = Settings::where('name', 'owe_count')->get(['values'])->first();
        $signs_sql = Settings::where('name', 'sign')->get(['values']);
        $signs = collect([]);
        foreach ($signs_sql as $sign) {
            $signs->push(json_decode($sign['values']));
        }
        // return $signs;
        return view('settings.edit', \compact('logo_values', 'signs', 'organizations',
            'meternumber_code', 'payment_expired_date', 'owe_count'));
    }

}