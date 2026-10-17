<?php

namespace Database\Seeders;

use App\Models\KeptKaya\KpTbankUnits;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KeptKaya_ItemUnitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $units = [
            ['name' => 'กิโลกรัม', 'shortname' => 'กก.'],
            ['name' => 'ชิ้น', 'shortname' => 'ชิ้น'],
            ['name' => 'ขวด', 'shortname' => 'ขวด'],
            ['name' => 'กล่อง', 'shortname' => 'กล่อง'],
        ];
        foreach($units as $unit){
            KpTbankUnits::create([
                'unitname' => $unit['name'],
                'unit_short_name' => $unit['shortname'],
                'status' => 'active',
                'deleted' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }
}
