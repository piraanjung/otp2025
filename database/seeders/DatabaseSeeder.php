<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RoleSeeder::class);
        $this->call(AdminSeeder::class);
        $this->call(ProvincesSeeder::class);
        $this->call(DistrictsSeeder::class);
        $this->call(TambonsSeeder::class);
        $this->call(InitSetingsSeeder::class);
        $this->call(SettingsSeeder::class);
        $this->call(ZonesSeeder::class);
        $this->call(SubzonesSeeder::class);
    }
}
