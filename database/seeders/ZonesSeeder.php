<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ZonesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('zones')->insert([
            ['zone_name' => 'หมู่ 1' , 'location' => 'ตำบลห้องแซง อำเภอเลิงนกทา จังหวัดยโสธร', 'status' => 'active', 'tambon_id' => '350805'],
            ['zone_name' => 'หมู่ 2' , 'location' => 'ตำบลห้องแซง อำเภอเลิงนกทา จังหวัดยโสธร', 'status' => 'active', 'tambon_id' => '350805'],
            ['zone_name' => 'หมู่ 3' , 'location' => 'ตำบลห้องแซง อำเภอเลิงนกทา จังหวัดยโสธร', 'status' => 'active', 'tambon_id' => '350805'],
            ['zone_name' => 'หมู่ 4' , 'location' => 'ตำบลห้องแซง อำเภอเลิงนกทา จังหวัดยโสธร', 'status' => 'active', 'tambon_id' => '350805'],
            ['zone_name' => 'หมู่ 5' , 'location' => 'ตำบลห้องแซง อำเภอเลิงนกทา จังหวัดยโสธร', 'status' => 'active', 'tambon_id' => '350805'],
            ['zone_name' => 'หมู่ 6' , 'location' => 'ตำบลห้องแซง อำเภอเลิงนกทา จังหวัดยโสธร', 'status' => 'active', 'tambon_id' => '350805'],
            ['zone_name' => 'หมู่ 7' , 'location' => 'ตำบลห้องแซง อำเภอเลิงนกทา จังหวัดยโสธร', 'status' => 'active', 'tambon_id' => '350805'],
            ['zone_name' => 'หมู่ 8' , 'location' => 'ตำบลห้องแซง อำเภอเลิงนกทา จังหวัดยโสธร', 'status' => 'active', 'tambon_id' => '350805'],
            ['zone_name' => 'หมู่ 9' , 'location' => 'ตำบลห้องแซง อำเภอเลิงนกทา จังหวัดยโสธร', 'status' => 'active', 'tambon_id' => '350805'],
            ['zone_name' => 'หมู่ 10', 'location' => 'ตำบลห้องแซง อำเภอเลิงนกทา จังหวัดยโสธร', 'status' => 'active', 'tambon_id' => '350805'],
            ['zone_name' => 'หมู่ 11', 'location' => 'ตำบลห้องแซง อำเภอเลิงนกทา จังหวัดยโสธร', 'status' => 'active', 'tambon_id' => '350805'],
            ['zone_name' => 'หมู่ 12', 'location' => 'ตำบลห้องแซง อำเภอเลิงนกทา จังหวัดยโสธร', 'status' => 'active', 'tambon_id' => '350805'],
            ['zone_name' => 'หมู่ 13', 'location' => 'ตำบลห้องแซง อำเภอเลิงนกทา จังหวัดยโสธร', 'status' => 'active', 'tambon_id' => '350805'],
            ['zone_name' => 'หมู่ 14', 'location' => 'ตำบลห้องแซง อำเภอเลิงนกทา จังหวัดยโสธร', 'status' => 'active', 'tambon_id' => '350805'],
            ['zone_name' => 'หมู่ 15', 'location' => 'ตำบลห้องแซง อำเภอเลิงนกทา จังหวัดยโสธร', 'status' => 'active', 'tambon_id' => '350805'],
            ['zone_name' => 'หมู่ 16', 'location' => 'ตำบลห้องแซง อำเภอเลิงนกทา จังหวัดยโสธร', 'status' => 'active', 'tambon_id' => '350805'],
            ['zone_name' => 'หมู่ 17', 'location' => 'ตำบลห้องแซง อำเภอเลิงนกทา จังหวัดยโสธร', 'status' => 'active', 'tambon_id' => '350805'],
            ['zone_name' => 'หมู่ 18', 'location' => 'ตำบลห้องแซง อำเภอเลิงนกทา จังหวัดยโสธร', 'status' => 'active', 'tambon_id' => '350805'],
            ['zone_name' => 'หมู่ 19', 'location' => 'ตำบลห้องแซง อำเภอเลิงนกทา จังหวัดยโสธร', 'status' => 'active', 'tambon_id' => '350805']
               ]);
    }
}
