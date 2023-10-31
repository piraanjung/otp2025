<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $settings =
        [
           [
                'name' => 'tambon_infos',
                'values' => json_encode(
                    [
                        'name' =>  'เทศบาลตำบลห้องแซง',
                        'short_name' =>  'ทต.ห้องแซง',
                        'address' => '222',
                        'moo' =>  17,
                        'tambon' => 'ห้องแซง',
                        'district' => 'เลิงนกทา',
                        'province' => 'ยโสธร',
                        'postcard' => '35120',
                        'phone' => '045-234332',
                        'logo' => 'img/hslogo.jpg'
                    ]
                ),
                'comment' => 'ข้อมูลทั่วไปของเทศบาล'

           ],
           [
               'name' => 'invoice_expired',
               'values' => 30,
               'comment' => 'ช่วงเวลาให้มาชำระเงินนับตั้งแต่วันที่ได้รับใบแจ้งหนี้'
           ]
        ];

        foreach($settings as $setting){
            DB::table('settings')->insert([
                'name' => $setting['name'],
                'values' => $setting['values'],
                'comment' => $setting['comment'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

    }
}
