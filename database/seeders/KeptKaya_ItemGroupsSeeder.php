<?php

namespace Database\Seeders;

use App\Models\KeptKaya\KpTbankItemsGroups;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KeptKaya_ItemGroupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $groups = [
            ['name' => 'เหล็ก', 'code' => 'IRN'],
            ['name' => 'ขวดพลาสติก', 'code' => 'PLT'], 
            ['name' => 'แก้ว', 'code' => 'GLS'], 
            ['name' => 'กระดาษ', 'code' => 'PAP'], 
        ];

        foreach($groups as $group){
            KpTbankItemsGroups::create([
                    'kp_items_groupname' => $group['name'],
                    'kp_items_group_code' => $group['code'],
                    'status' => 'active',
                    'deleted' => '0',
                    'sequence_num' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        }
       
    }
}
