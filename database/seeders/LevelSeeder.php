<?php

namespace Database\Seeders;

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
            ['name' => 'Basic', 'amount' => 5, 'reg_bonus' => 1, 'ref_bonus' => 1, 'min_withdrawal' => 20, 'earning_per_view' => 0.003, 'earning_per_like'=>0, 'earning_per_comment' => 0],
            ['name' => 'Premium', 'amount' => 10, 'reg_bonus' => 3, 'ref_bonus' => 3, 'min_withdrawal' => 15, 'earning_per_view' => 0.005, 'earning_per_like'=>0.03, 'earning_per_comment' => 0.03],
        ];

        foreach($levels as $level){
            Level::create($level);
        }

    }
}
