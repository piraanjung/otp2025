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
        DB::table('subzone')->insert([
            ['subzone_name' =>'หมู่ 1',  'zone_id' => '1'],
            ['subzone_name' =>'หมู่ 1-2','zone_id' => '1'],
            ['subzone_name' =>'หมู่ 2',  'zone_id' => '2'],
            ['subzone_name' =>'หมู่ 3',  'zone_id' => '3'],
            ['subzone_name' =>'หมู่ 4',  'zone_id' => '4'],
            ['subzone_name' =>'หมู่ 5',  'zone_id' => '5'],
        ]);
    }
}
