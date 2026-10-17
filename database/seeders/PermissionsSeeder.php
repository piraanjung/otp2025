<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions =[
             ['name' => 'access recycle bank'],
             ['name' => 'access recycle bank mobile'],
             ['name' => 'access annual fee'],
             ['name' => 'access tabwater'],
             ['name' => 'access tabwater mobile'],
        ];

        foreach($permissions as $permission){
            Permission::create([
                'name' => $permission['name'],
                'guard_name' => 'web'
            ]);
        }
    }
}
