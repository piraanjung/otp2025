<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubzonesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('subzones')->insert([
                    ['id' => '1',  'zone_id' =>	'1',	'subzone_name' => 'หมู่1'	        , 'status' => 'active'],
                    ['id' => '2',  'zone_id' =>	'2',	'subzone_name' => 'หมู่2'	        , 'status' => 'active'],
                    ['id' => '3',  'zone_id' =>	'3',	'subzone_name' => 'หมู่3'	        , 'status' => 'active'],
                    ['id' => '4',  'zone_id' =>	'4',	'subzone_name' => 'หมู่4'	        , 'status' => 'active'],
                    ['id' => '5',  'zone_id' =>	'5',	'subzone_name' => 'หมู่5'	        , 'status' => 'active'],
                    ['id' => '6',  'zone_id' =>	'6',	'subzone_name' => 'หมู่6'	        , 'status' => 'active'],
                    ['id' => '7',  'zone_id' =>	'7',	'subzone_name' => 'หมู่ 7'	        , 'status' => 'active'],
                    ['id' => '8',  'zone_id' =>	'8',	'subzone_name' => 'หมู่8'	        , 'status' => 'active'],
                    ['id' => '9',  'zone_id' =>	'9',	'subzone_name' => 'หมู่9'	        , 'status' => 'active'],
                    ['id' => '10', 'zone_id' =>	'10',	'subzone_name' => 'หมู่10'	        , 'status' => 'active'],
                    ['id' => '11', 'zone_id' =>	'11',	'subzone_name' => 'หมู่11'	        , 'status' => 'active'],
                    ['id' => '12', 'zone_id' =>	'12',	'subzone_name' => 'หมู่12'	        , 'status' => 'active'],
                    ['id' => '14', 'zone_id' =>	'14',	'subzone_name' => 'หมู่14'	        , 'status' => 'active'],
                    ['id' => '15', 'zone_id' =>	'15',	'subzone_name' => 'หมู่15'	        , 'status' => 'active'],
                    ['id' => '16', 'zone_id' =>	'16',	'subzone_name' => 'หมู่16'	        , 'status' => 'active'],
                    ['id' => '17', 'zone_id' =>	'17',	'subzone_name' => 'หมู่17'	        , 'status' => 'active'],
                    ['id' => '18', 'zone_id' =>	'18',	'subzone_name' => 'หมู่18'	        , 'status' => 'active'],
                    ['id' => '19', 'zone_id' =>	'19',	'subzone_name' => 'หมู่19'	        , 'status' => 'active'],
                    ['id' => '20', 'zone_id' =>	'13',   'subzone_name' => 'เส้น 13-1'	   , 'status' => 'active'],
                    ['id' => '21', 'zone_id' =>	'13',	'subzone_name' => 'เส้น 13-2'	   , 'status' => 'active'],
                    ['id' => '35', 'zone_id' =>	'3' ,	'subzone_name' => 'หมู่3-1'	        , 'status' => 'active'],
        ]);
    }
}
