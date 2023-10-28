<?php

namespace Database\Seeders;

use App\Models\MeterType;
use App\Models\NumberSequence;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InitSetingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        NumberSequence::create([
            "nsq_id" => 1,
            "meternumber" => 1,
        ]);

        MeterType::create([

            'meter_type_name' => 'มิเตอร์โรงงาน',
            'price_per_unit' => 12.0,
            'metersize' => 22,

        ]);
        MeterType::create([
            'meter_type_name' => 'มิเตอร์ครัวเรือน',
            'price_per_unit' => 8.0,
            'metersize' => 12,
        ]);


    }
}
