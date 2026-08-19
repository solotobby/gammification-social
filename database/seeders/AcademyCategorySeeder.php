<?php

namespace Database\Seeders;

use App\Models\AcademyCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AcademyCategorySeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'Monetization',
            'Growth',
            'AI',
            'Communities',
            'Creator Economy',
            'Students',
            'Business',
            'News',
        ];

        foreach ($names as $name) {
            AcademyCategory::query()->firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }
    }
}
