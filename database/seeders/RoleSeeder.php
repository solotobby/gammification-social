<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['id'=>1, 'name'=>'admin', 'guard_name'=>'web'],
            ['id'=>2, 'name'=>'user', 'guard_name'=>'web'],
            ['id'=>3, 'name'=>'staff', 'guard_name'=>'web']
        ];

        foreach($roles as $role){
            Role::create($role);
        }
    }
}
