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
       $user1 = User::create([
        'username' => 'super admin',
        'password'=> bcrypt('s@admin'),
        'prefix'=> 'นาย',
        'firstname'=> 'super',
        'lastname'=> 'admin',
        'email' => 'admin@gmail.com',
        'email_verified_at' => now(),
        'id_card'=> '111111111',
        'phone'=> '0999999999',
        'gender'=> 'm',
        'address'=> 'x',
        'zone_id'=> '1',
        'subzone_id'=> '1',
        'tambon_code'=> '1',
        'district_code'=> '1',
        'province_code'=> '1',
        'role_id' => '1,2'
        ])->assignRole(['super admin', 'admin']);


        User::create([
            'username' => 'admin1',
            'password'=> bcrypt('999999999'),
            'prefix'=> 'นางสาว',
            'firstname'=> 'admin1',
            'lastname'=> 'admin1',
            'email' => 'admin1@gmail.com',
            'email_verified_at' => now(),
            'id_card'=> '111111111',
            'phone'=> '0999999999',
            'gender'=> 'm',
            'address'=> 'x',
            'zone_id'=> '1',
            'subzone_id'=> '1',
            'tambon_code'=> '1',
            'district_code'=> '1',
            'province_code'=> '1',
            'role_id' => '2'
            ])->assignRole('admin');

    }
}
