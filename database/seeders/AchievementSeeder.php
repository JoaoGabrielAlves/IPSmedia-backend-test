<?php

namespace Database\Seeders;

use App\Enums\AchievementCategoryEnum;
use App\Models\Achievement;
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
                'category' => AchievementCategoryEnum::LESSONS_WATCHED,
                'requirement' => 1,
            ],
            [
                'name' => '5 Lessons Watched',
                'category' => AchievementCategoryEnum::LESSONS_WATCHED,
                'requirement' => 5,
            ],
            [
                'name' => '10 Lessons Watched',
                'category' => AchievementCategoryEnum::LESSONS_WATCHED,
                'requirement' => 10,
            ],
            [
                'name' => '25 Lessons Watched',
                'category' => AchievementCategoryEnum::LESSONS_WATCHED,
                'requirement' => 25,
            ],
            [
                'name' => '50 Lessons Watched',
                'category' => AchievementCategoryEnum::LESSONS_WATCHED,
                'requirement' => 50,
            ],
            [
                'name' => 'First Comment Written',
                'category' => AchievementCategoryEnum::COMMENTS_WRITTEN,
                'requirement' => 1,
            ],
            [
                'name' => '3 Comments Written',
                'category' => AchievementCategoryEnum::COMMENTS_WRITTEN,
                'requirement' => 3,
            ],
            [
                'name' => '5 Comments Written',
                'category' => AchievementCategoryEnum::COMMENTS_WRITTEN,
                'requirement' => 5,
            ],
            [
                'name' => '10 Comments Written',
                'category' => AchievementCategoryEnum::COMMENTS_WRITTEN,
                'requirement' => 10,
            ],
            [
                'name' => '20 Comments Written',
                'category' => AchievementCategoryEnum::COMMENTS_WRITTEN,
                'requirement' => 20,
            ],
        ];

        Achievement::query()->upsert($achievements, 'name');
    }
}
