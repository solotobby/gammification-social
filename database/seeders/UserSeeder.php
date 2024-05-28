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
            ['id' => 1, 'name'=>'Oluwatobi Solomon', 'username'=>'solando', 'email'=>'solando@pawo.com', 'password'=>bcrypt('Solomon001'), 'referral_code'=>'hdsjlgfsjgfjs', 'phone'=>'07774276007', 'access_code_id' => null],
            ['id' => 2, 'name'=>'Fantastic Four', 'username'=>'ff4', 'email'=>'ff4@ff4.com', 'password'=>bcrypt('Solomon001'), 'referral_code'=>'hdsjlgfsODFS', 'phone'=>'0777427091', 'access_code_id' => null]
        ];

        $roleId = Role::where('name', 'admin')->first()->id;

        foreach($users as $user){

            $reg = User::create($user);
            $reg->assignRole($roleId);

            Wallet::create(['user_id' => $reg->id, 'balance' => '0.00', 'currency' => 'USD']);

        }
    }
}
