<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles =['Super Admin', 'Admin', 'Tabwater Header', 'Tabwater Staff', 
                'Finance Header', 'Finance Staff', "User"];
        foreach($roles as $role){
            Role::create(['name' => $role]);
        }
    }
}
