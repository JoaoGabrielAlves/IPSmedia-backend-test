<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $achievements = [
            [
                'name' => 'First Lesson Watched',
                'category' => 'Lessons Watched',
                'requirement' => 1
            ],
            [
                'name' => '5 Lessons Watched',
                'category' => 'Lessons watched',
                'requirement' => 5
            ],
            [
                'name' => '10 Lessons Watched',
                'category' => 'Lessons watched',
                'requirement' => 10
            ],
            [
                'name' => '25 Lessons Watched',
                'category' => 'Lessons watched',
                'requirement' => 25
            ],
            [
                'name' => '50 Lessons Watched',
                'category' => 'Lessons watched',
                'requirement' => 50
            ],
            [
                'name' => 'First Comment Written',
                'category' => 'Comments Written',
                'requirement' => 1
            ],
            [
                'name' => '3 Comments Written',
                'category' => 'Comments Written',
                'requirement' => 3
            ],
            [
                'name' => '5 Comments Written',
                'category' => 'Comments Written',
                'requirement' => 5
            ],
            [
                'name' => '10 Comments Written',
                'category' => 'Comments Written',
                'requirement' => 10
            ],
            [
                'name' => '20 Comments Written',
                'category' => 'Comments Written',
                'requirement' => 20
            ],
        ];

        Achievement::query()->upsert($achievements, 'name');
    }
}
