<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $badges = [
            [
                'name' => '0 Achievements',
                'requirement' => 0,
            ],
            [
                'name' => '4 Achievements',
                'requirement' => 4,
            ],
            [
                'name' => '8 Achievements',
                'requirement' => 8,
            ],
            [
                'name' => '10 Achievements',
                'requirement' => 10,
            ]
        ];

        Badge::query()->upsert($badges, 'name');
    }
}
