<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin\OrganizationType;
class Organizations1TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orgsTypes =[
                ["name" =>"เทศบาลตำบล", "code" => "ทต."],
                ["name" =>"องค์การบริหารส่วนตำบล", "code" => "อบต."],
                ["name" =>"เทศบาลเมือง", "code" => "ทม."],
                ["name" =>"โรงเรียน", "code" => "รร."],
                ["name" =>"โรงพยาบาล", "code" => "รพ."],
        	];

            foreach($orgsTypes as $orgsType){
                OrganizationType::create([
                    "name" => $orgsType['name'],
                    "code" => $orgsType['code'],
                    "status" => 1,
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s'),
                ]);
            }

    }
}
