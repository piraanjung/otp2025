<?php

namespace Database\Seeders;

use App\Models\Admin\UserProfile;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {


     

        // 3. สร้าง หรือ อัปเดต บัญชีผู้ใช้ Super Admin
         User::firstOrCreate(
            ['username' => 'superadmin'],
            [
            'org_id_fk' => 1,
            'password' => Hash::make('s@admin'),
            'prefix' => 'นาย',
            'firstname' => 'super',
            'lastname' => 'admin',
            'email' => 'admin@gmail.com',
            'email_verified_at' => now(),
            'id_card' => '111111111',
            'phone' => '0999999999',
            'gender' => 'm',
            'address' => 'x',
            'zone_id' => '1',
            'subzone_id' => '1',
            'tambon_code' => '350805',
            'district_code' => '3508',
            'province_code' => '35',
          ])->assignRole(['Super Admin']);
          

       

        $this->command->info('Super Admin created successfully!');
        $this->command->info('Username: superadmin');
        $this->command->info('Password: 12345678');
     

  }
}
