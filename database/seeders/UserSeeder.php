<?php

namespace Database\Seeders;


use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            ['id' => 1, 'name'=>'Pawo', 'username'=>'pawo', 'email'=>'pawo@gmail.com', 'password'=>bcrypt('Solomon001'), 'referral_code'=>'hdsjlgfsjgfjs', 'phone'=>'07774276007']
        ];

        $roleId = Role::where('name', 'admin')->first()->id;

        foreach($users as $user){

            $reg = User::create($user);
            $reg->assignRole($roleId);

            Wallet::create(['user_id' => $reg->id, 'balance' => '0.00', 'currency' => 'USD']);

        }
    }
}
