<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\DistrictsExport;
use App\Exports\OrganizationsExport;
use App\Exports\ProvincesExport;
use App\Exports\TambonsExport;
use App\Exports\TwMetersExport;
use App\Exports\TWZoneBlocksExport;
use App\Exports\TWZonesExport;
use App\Exports\UsersExport;
use App\Http\Controllers\FunctionsController;
use App\Imports\DistrictsImport;
use App\Imports\OrganizationsImport;
use App\Imports\ProvinceDataImport;
use App\Imports\TambonsImport;
use App\Imports\TwMetersImport;
use App\Imports\TWZoneBlocksImport;
use App\Imports\TwZonesImport;
use App\Imports\UsersImport;
use App\Models\AsMembers;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel; // อย่าลืม import Facade
use App\Imports\YourDataImport; // สร้าง Importer ในขั้นตอนถัดไป
use App\Models\SequenceNumber;
use App\Models\Tabwater\TwMerterInfos;
use App\Models\Tabwater\TwMeters;
use App\Models\User;

class SuperAdminSettingsController extends Controller
{
    // แสดงหน้า Settings พร้อมฟอร์ม Import Excel
    public function showSettingsForm()
    {
        return view('superadmin.settings');
    }

    public function userToTabwater(){
         $users= User::role('user')->with([
            'as_members' =>function($q){
                return $q->select('*')->where('tabwater', '0');
            }
        ])->get('id');
        $userFilters = collect($users)->filter(function($v){
             return collect($v->as_members)->isNotEmpty();
        });


        $tw_sq_number = SequenceNumber::get('tabwater')[0];
       
        foreach($userFilters as $user){
            TwMerterInfos::create([
                // 'id' => $tw_sq_number->tabwater++,
                // 'user_id' => $user->id,
                // 'meternumber' => FunctionsController::createMeterNumberString($user->id),
                // 'factory_no' => 'zz',
                // 'middle_name' => '',
                // 'meter_address' => ,
                // 'undertake_zone_id' => ,
                // 'undertake_subzone_id' => ,
                // 'acceptace_date' => ,
                // 'status' => ,
                // 'comment' => ,
                // 'metertype_id' => ,
                // 'next_inv_no' => ,
                // 'init_reading' => ,
                // 'owe_count' => ,
                // 'current_active_reading' => ,
                // 'cutmeter_status' => ,
                // 'payment_id_fk' => ,
                // 'discounttype' => ,
                // 'recorder_id' => ,
            ]);
        }

    }

    // จัดการการ Import Excel
   public function importProvinces(Request $request)
    {
        $request->validate([
            'provinces_excel_file' => 'required|mimes:xls,xlsx|max:10240', // ตรวจสอบไฟล์ Excel
        ]);

        try {
            Excel::import(new ProvinceDataImport, $request->file('provinces_excel_file'));
            return back()->with('success', 'Provinces data imported successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error importing provinces data: ' . $e->getMessage());
        }
    }

    // เพิ่มเมธอดสำหรับ Export Provinces
    public function exportProvinces()
    {
        // กำหนดชื่อไฟล์ที่จะดาวน์โหลด
        $fileName = 'provinces_data_' . date('Ymd_His') . '.xlsx';
        return Excel::download(new ProvincesExport, $fileName);
    }

    public function importDistricts(Request $request)
    {
        $request->validate([
            'districts_excel_file' => 'required|mimes:xls,xlsx|max:10240',
        ]);

        try {
            Excel::import(new DistrictsImport, $request->file('districts_excel_file'));
            return back()->with('success', 'Districts data imported successfully!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            // ดักจับข้อผิดพลาดจากการตรวจสอบข้อมูล (Validation)
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }
            return back()->with('error', 'Validation errors: ' . implode('; ', $errors));
        } catch (\Exception $e) {
            return back()->with('error', 'Error importing districts data: ' . $e->getMessage());
        }
    }

    // เพิ่มเมธอดสำหรับ Export Districts
    public function exportDistricts()
    {
        $fileName = 'districts_data_' . date('Ymd_His') . '.xlsx';
        return Excel::download(new DistrictsExport, $fileName);
    }

    // เพิ่มเมธอดสำหรับ Import Tambons
    public function importTambons(Request $request)
    {
        $request->validate([
            'tambons_excel_file' => 'required|mimes:xls,xlsx|max:10240',
        ]);

        try {
            Excel::import(new TambonsImport, $request->file('tambons_excel_file'));
            return back()->with('success', 'Tambons data imported successfully!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors()) . ' (Value: ' . implode(', ', $failure->values()) . ')';
            }
            return back()->with('error', 'Validation errors: ' . implode('; ', $errors));
        } catch (\Exception $e) {
            return back()->with('error', 'Error importing tambons data: ' . $e->getMessage());
        }
    }

    // เพิ่มเมธอดสำหรับ Export Tambons
    public function exportTambons()
    {
        $fileName = 'tambons_data_' . date('Ymd_His') . '.xlsx';
        return Excel::download(new TambonsExport, $fileName);
    }

    // เพิ่มเมธอดสำหรับ Import Zones
    public function importTWZones(Request $request)
    {
        $request->validate([
            'tw_zones_excel_file' => 'required|mimes:xls,xlsx|max:10240',
        ]);

        try {
            Excel::import(new TwZonesImport, $request->file('tw_zones_excel_file'));
            return back()->with('success', 'TWZones data imported successfully!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors()) . ' (Value: ' . implode(', ', $failure->values()) . ')';
            }
            return back()->with('error', 'Validation errors: ' . implode('; ', $errors));
        } catch (\Exception $e) {
            return back()->with('error', 'Error importing zones data: ' . $e->getMessage());
        }
    }

    // เพิ่มเมธอดสำหรับ Export Zones
    public function exportTWZones()
    {
        $fileName = 'zones_data_' . date('Ymd_His') . '.xlsx';
        return Excel::download(new TWZonesExport, $fileName);
    }

    public function importTWZoneBlocks(Request $request)
    {
        $request->validate([
            'tw_zoneblocks_excel_file' => 'required|mimes:xls,xlsx|max:10240',
        ]);

        try {
            Excel::import(new TWZoneBlocksImport, $request->file('tw_zoneblocks_excel_file'));
            return back()->with('success', 'Zone Blocks data imported successfully!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors()) . ' (Values: ' . implode(', ', $failure->values()) . ')';
            }
            return back()->with('error', 'Validation errors: ' . implode('; ', $errors));
        } catch (\Exception $e) {
            return back()->with('error', 'Error importing Zone Blocks data: ' . $e->getMessage());
        }
    }

    // เพิ่มเมธอดสำหรับ Export TW_ZoneBlocks
    public function exportTWZoneBlocks()
    {
        $fileName = 'tw_zoneblocks_data_' . date('Ymd_His') . '.xlsx';
        return Excel::download(new TWZoneBlocksExport, $fileName);
    }

    // เพิ่มเมธอดสำหรับ Import Organizations
    public function importOrganizations(Request $request)
    {
        $request->validate([
            'organizations_excel_file' => 'required|mimes:xls,xlsx|max:10240',
        ]);

        try {
            Excel::import(new OrganizationsImport, $request->file('organizations_excel_file'));
            return back()->with('success', 'Organizations data imported successfully!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors()) . ' (Values: ' . implode(', ', $failure->values()) . ')';
            }
            return back()->with('error', 'Validation errors: ' . implode('; ', $errors));
        } catch (\Exception $e) {
            return back()->with('error', 'Error importing Organizations data: ' . $e->getMessage());
        }
    }

    // เพิ่มเมธอดสำหรับ Export Organizations
    public function exportOrganizations()
    {
        $fileName = 'organizations_data_' . date('Ymd_His') . '.xlsx';
        return Excel::download(new OrganizationsExport, $fileName);
    }

    public function importUsers(Request $request)
    {
        set_time_limit(600); 
        $request->validate([
            'users_excel_file' => 'required|mimes:xls,xlsx|max:10240', // ตรวจสอบไฟล์ Excel
        ]);

        try {
            Excel::import(new UsersImport, $request->file('users_excel_file'));
            return back()->with('success', 'Users data imported successfully!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors()) . ' (Values: ' . implode(', ', $failure->values()) . ')';
            }
            return back()->with('error', 'Validation errors: ' . implode('; ', $errors));
        } catch (\Exception $e) {
            return back()->with('error', 'Error importing users data: ' . $e->getMessage());
        }
    }

    // เพิ่มเมธอดสำหรับ Export Users
    public function exportUsers()
    {
        $fileName = 'users_data_' . date('Ymd_His') . '.xlsx';
        return Excel::download(new UsersExport, $fileName);
    }

    public function importTwMeters(Request $request)
    {
        set_time_limit(600); 

        $request->validate([
            'tw_meters_excel_file' => 'required|mimes:xls,xlsx|max:10240', // ตรวจสอบไฟล์ Excel
        ]);

        try {
            Excel::import(new TwMetersImport, $request->file('tw_meters_excel_file'));
            return back()->with('success', 'Meters data imported successfully!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors()) . ' (Values: ' . implode(', ', $failure->values()) . ')';
            }
            return back()->with('error', 'Validation errors: ' . implode('; ', $errors));
        } catch (\Exception $e) {
            return back()->with('error', 'Error importing meters data: ' . $e->getMessage());
        }
    }

    // เพิ่มเมธอดสำหรับ Export TwMeters
    public function exportTwMeters()
    {
        $fileName = 'tw_meters_data_' . date('Ymd_His') . '.xlsx';
        return Excel::download(new TwMetersExport, $fileName);
    }
}