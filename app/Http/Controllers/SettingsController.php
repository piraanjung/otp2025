<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\FunctionsController;
use App\Models\SequenceNumber;
use App\Models\Setting;
use App\Models\Staff;
use App\Models\User;
use App\Models\UserMerterInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as ReaderXlsx;
use Illuminate\Support\Facades\Auth;

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

    public function invoice()
    {
        $meternumber_code = Setting::where('name', 'meternumber_code')->get(['values'])->first();
        $invoice_expired = Setting::where('name', 'invoice_expired')->get(['values'])->first();
        $owe_count = Setting::where('name', 'owe_count')->get(['values'])->first();
        $vat = Setting::where('name', 'vat')->get(['values'])->first();

        return view(
            'admin.settings.invoice.index',
            \compact(
                'meternumber_code',
                'invoice_expired',
                'vat',
                'owe_count'
            )
        );
    }

    public function update_invoice_and_vat(REQUEST $request)
    {
        Setting::where('name', 'meternumber_code')->update([
            'values' => $request->get('meternumber_code'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),

        ]);
        Setting::where('name', 'owe_count')->update([
            'values' => $request->get('owe_count'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),

        ]);
        Setting::where('name', 'vat')->update([
            'values' => $request->get('vat'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),

        ]);
        Setting::where('name', 'invoice_expired')->update([
            'values' => $request->get('invoice_expired'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),

        ]);



        return redirect()->back()->with(['message' => 'ทำการบันทึกข้อมูลแล้ว', 'color' => 'success']);
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

        Setting::where('id', 5)->update([
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

    public function store_users(REQUEST $request)
    {
        //Allowed mime types
        $excelMimes = array('text/xls', 'text/xlsx', 'application/excel', 'application/vnd.msexcel', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        // Validate whether selected file is a Excel file
        if (!empty ($_FILES['file']['name']) && in_array($_FILES['file']['type'], $excelMimes)) {
            // If the file is uploaded
            if (is_uploaded_file($_FILES['file']['tmp_name'])) {
                $reader = new ReaderXlsx();
                $spreadsheet = $reader->load($_FILES['file']['tmp_name']);
                $worksheet = $spreadsheet->getActiveSheet();
                $worksheet_arr = $worksheet->toArray();

                // Remove header row
                unset($worksheet_arr[0]);
                unset($worksheet_arr[1]);

                $user_array = [];
                $userMeterInmfos_array = [];
                $userNumberSq = SequenceNumber::where('id', 1)->get('user')->first();
                $i = $userNumberSq->user;
                // set_time_limit(0);

                foreach ($worksheet_arr as $row) {
                    $user_array[] = [
                        "username"          => 'user' . $i,
                        "password"          => $row[2],
                        "prefix"            => $row[3],
                        "firstname"         => $row[4],
                        "lastname"          => $row[5],
                        "email"             => $row[6],
                        "line_id"           => $row[7],
                        "id_card"           => $row[8],
                        "phone"             => $row[9],
                        "gender"            => $row[10],
                        "address"           => $row[11],
                        "zone_id"           => $row[12],
                        "subzone_id"        => $row[13],
                        "tambon_code"       => $row[14],
                        "district_code"     => $row[15],
                        "province_code"     => $row[16],
                        "email_verified_at" => date('Y-m-d H:i:s'),
                        "remember_token"     => '',
                        "role_id"           => $row[17],
                        "status"            => "active",
                        "created_at"        => date('Y-m-d H:i:s'),
                        "updated_at"        => date('Y-m-d H:i:s'),
                    ];
                    $userMeterInmfos_array[] = [
                        "meter_id"              => $row[0],
                        "user_id"               => $i++,
                        "meternumber"           => FunctionsController::createMeterNumberString($row[0]),
                        "metertype_id"          => $row[18],
                        "meter_address"         => $row[19],
                        "undertake_zone_id"     => $row[20],
                        "undertake_subzone_id"  => $row[21],
                        "acceptace_date"        => date('Y-m-d'),
                        "payment_id"            => $row[22] == "เงินสด" ? 1 : 2,
                        "discounttype"          => $row[23],
                        "status"                => "active",
                        "comment"               => "",
                        "owe_count"             => 0,
                        "recorder_id"           => Auth::id(),
                        "created_at"            => date('Y-m-d H:i:s'),
                        "updated_at"            => date('Y-m-d H:i:s'),
                    ];
                }

                $user_cols = collect($user_array[0])->count();
                $user_limit = collect($user_array)->count(); // Mysql placeholder limit ...
                collect($user_array)->chunk( floor($user_limit / $user_cols) )
                    ->each(function($calls) {
                        User::insert($calls->toArray());
                    });

                $usermeterinfo_cols = collect($userMeterInmfos_array[0])->count();
                $usermeterinfo_limit = collect($userMeterInmfos_array)->count(); // Mysql placeholder limit ...
                collect($userMeterInmfos_array)->chunk(floor($usermeterinfo_limit / $usermeterinfo_cols))
                ->each(function($calls){
                    UserMerterInfo::insert($calls->toArray());
                });


                SequenceNumber::where('id', 1)->update([
                    'user' => $userNumberSq->user + collect($user_array)->count(),
                ]);

                foreach ($userMeterInmfos_array as $user) {
                    $user = User::where('id', $user['user_id'])->assignRole('user');
                }
            }
        }

        // return redirect('settings/import_excel/' . $request->get("info_type"));
    }



}
//ให้ทำการ Insert data
// $user = new User();
// $user->username = 'user' . $row[1];
// $user->password = Hash::make($row[2]);
// $user->prefix = $row[3];
// $user->firstname = $row[4];
// $user->lastname = $row[5];
// $user->email = $row[6];
// $user->line_id = $row[7];
// $user->id_card = $row[8];
// $user->phone = $row[9];
// $user->gender = $row[10];
// $user->address = $row[11];
// $user->zone_id = $row[12];
// $user->subzone_id = $row[13];
// $user->tambon_code = $row[14];
// $user->district_code = $row[15];
// $user->province_code = $row[16];
// $user->email_verified_at = $row[17];
// $user->rememberToken = Hash::make($row[1]."".date('Y-m-d H:i:s'));
// $user->role_id = $row[18];
// $user->status = $row[19];
// $user->created_at = date('Y-m-d H:i:s');
// $user->updated_at = date('Y-m-d H:i:s');
// $user->save();



// $usermeterInfo = new UserMerterInfo;
// $usermeterInfo->user_id = $user->id();
// $usermeterInfo->meternumber = $row[17] . $this->meternumber($user->id());
// $usermeterInfo->metertype = $row[18];
// $usermeterInfo->counter_unit = $row[19];
// $usermeterInfo->metersize = $row[20];
// $usermeterInfo->undertake_zone_id = $row[21];
// $usermeterInfo->undertake_subzone_id = $row[22];
// $usermeterInfo->acceptace_date = date('Y-m-d');
// $usermeterInfo->payment_id = $row[23];
// $usermeterInfo->discounttype = $row[24];
// $usermeterInfo->recorder_id = Auth::id();
// $usermeterInfo->created_at = date('Y-m-d H:i:s');
// $usermeterInfo->updated_at = date('Y-m-d H:i:s');
// $usermeterInfo->save();
