<?php

namespace Database\Seeders;

use App\Models\Admin\UserProfile;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       User::create([
            'username' => 'super admin',
            'password'=> bcrypt('s@admin'),
            'email' => 'admin@gmail.com',
            'email_verified_at' => now(),
        ])->assignRole('super admin', 'admin');

        UserProfile::create([
            'user_id'=> 1,
            'name'=> 'super admin',
            'id_card'=> '111111111',
            'phone'=> '0999999999',
            'gender'=> 'm',
            'address'=> 'x',
            'zone_id'=> '0',
            'subzone_id'=> '0',
            'tambon_code'=> '0',
            'district_code'=> '0',
            'province_code'=> '0',
        ]);

    }
}
