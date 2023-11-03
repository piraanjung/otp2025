<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TambonsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tambonArray = [
// [3508, 350801, '*น้ำคำ', '35120'],
            // [3508, 350802, 'บุ่งค้า', '35120'],
            // [3508, 350803, 'สวาท', '35120'],
            // [3508, 350804, '*ส้มผ่อ', '35120'],
            [3508, 350805, 'ห้องแซง', '35120'],
            [3508, 350806, 'สามัคคี', '35120'],
            [3508, 350807, 'กุดเชียงหมี', '35120'],
            // [3508, 350808, '*คำเตย', '35120'],
            // [3508, 350809, '*คำไผ่', '35120'],
            // [3508, 350810, 'สามแยก', '35120'],
            // [3508, 350811, 'กุดแห่', '35120'],
            // [3508, 350812, 'โคกสำราญ', '35120'],
            // [3508, 350813, 'สร้างมิ่ง', '35120'],
            // [3508, 350814, 'ศรีแก้ว', '35120'],
            // [3508, 350815, '*ไทยเจริญ', '35120'],
            // [3508, 350895, '*ไทยเจริญ', '35120'],
            // [3508, 350896, '*คำไผ่', '35120'],
            // [3508, 350897, '*คำเตย', '35120'],
            // [3508, 350898, '*ส้มผ่อ', '35120'],
            // [3508, 350899, '*น้ำคำ', '35120'],
        ];
        foreach ($tambonArray as $tambon){
            DB::table('tambons')->insert([
                'id' => $tambon[1],
                'tambon_name' => $tambon[2],
                'district_id' => $tambon[0],
                'zipcode' => $tambon[3],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
}
