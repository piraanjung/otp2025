<?php

namespace Database\Seeders;

use App\Models\Admin\Organization;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrganizationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Organization::create([
            'org_code' => 'HSG1',
            'org_type_id' => 1,
            'org_name' => 'ห้องแซง',
            'org_address' => 222,
            'org_zone_id_fk' => null,
            'org_tambon_id_fk' => 350805,
            'org_district_id_fk' => 3508,
            'org_province_id_fk' => 35,
            'org_route' => null,
            'org_zipcode' => 35120,
            'org_phone'  => null,
            'org_dept_name' => 'งานประปา',
            'org_dept_short_name' => 'ปป.',
            'org_dept_phone' => null,
            'org_head_name' => null,
            'fin_head_name' => null,
            'tw_head_name' => null,
            'fin_head_license_img' => null,
            'org_head_license_img' => null,
            'tw_head_license_img' => null,
            'org_logo_img' => 'hs_logo.png',
            'meternumbercode' => null,
            'org_database' => 'envoso_hs1',
            'cutmeter_count' => 0,
            'vat'=> 0,
            'lat' => 0,
            'long' => 0,
         ]);


         Organization::create([
            'org_code' => 'TNG1',
            'org_type_id' => 5,
            'org_name' => 'เต่างอย',
            'org_address' => 80,
            'org_zone_id_fk' => null,
            'org_tambon_id_fk' => 350805,
            'org_district_id_fk' => 4714,
            'org_province_id_fk' => 47,
            'org_route' => null,
            'org_zipcode' => 35120,
            'org_phone'  => null,
            'org_dept_name' => 'งานประปา',
            'org_dept_short_name' => 'ปป.',
            'org_dept_phone' => null,
            'org_head_name' => null,
            'fin_head_name' => null,
            'tw_head_name' => null,
            'fin_head_license_img' => null,
            'org_head_license_img' => null,
            'tw_head_license_img' => null,
            'org_logo_img' => 'hs_logo.png',
            'meternumbercode' => null,
            'org_database' => 'envoso_hs1',
            'cutmeter_count' => 0,
            'vat'=> 0,
            'lat' => 0,
            'long' => 0,
         ]);


    }
}
