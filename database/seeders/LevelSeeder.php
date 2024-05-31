<?php

namespace Database\Seeders;

use App\Models\AccessCode;
use App\Models\Level;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $levels = [
            ['name' => 'Beginner', 'amount' => 5, 'reg_bonus' => 1, 'ref_bonus' => 1, 'min_withdrawal' => 20, 'earning_per_view' => 1, 'earning_per_like'=>0.5, 'earning_per_comment' => 0.5],
            ['name' => 'Creator', 'amount' => 10, 'reg_bonus' => 2, 'ref_bonus' => 3, 'min_withdrawal' => 15, 'earning_per_view' => 2, 'earning_per_like'=>0.6, 'earning_per_comment' => 0.7],
            ['name' => 'Influencer', 'amount' => 20, 'reg_bonus' => 5, 'ref_bonus' => 5, 'min_withdrawal' => 15, 'earning_per_view' => 4, 'earning_per_like'=>0.7, 'earning_per_comment' => 1],
        ];

        foreach($levels as $level){
            Level::create($level);

            
        }
        $level = Level::where('name', 'Influencer')->first();
        AccessCode::create(['tx_id' => time(),'name' =>'Influencer', 'email' => 'mark@gmail.com', 'amount' => 20, 'code' => 12345, 'level_id' => $level->id]);
        AccessCode::create(['tx_id' => time().'1','name' =>'Influencer', 'email' => 'marsk@gmail.com', 'amount' => 20, 'code' => 12345, 'level_id' => $level->id]);
        AccessCode::create(['tx_id' => time().'10','name' =>'Influencer', 'email' => 'marsk@gmail.com', 'amount' => 20, 'code' => 12345, 'level_id' => $level->id]);
            

            
        



    }
}
