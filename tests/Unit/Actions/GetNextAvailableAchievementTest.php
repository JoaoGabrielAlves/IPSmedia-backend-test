<?php

namespace Tests\Unit\Actions;

use App\Actions\GetNextAvailableAchievement;
use App\Actions\UnlockAchievement;
use App\Enums\AchievementCategoryEnum;
use App\Models\Achievement;
use App\Models\User;
use Tests\TestCase;

class GetNextAvailableAchievementTest extends TestCase
{
    /** @test */
    public function it_should_get_next_available_achievement()
    {
        $user = User::factory()->create();

        $category = AchievementCategoryEnum::COMMENTS_WRITTEN;

        $firstCommentAchievement = Achievement::query()
            ->where('category', '=', $category)
            ->orderBy('requirement')
            ->first();

        UnlockAchievement::handle($firstCommentAchievement, $user);

        $achievement = GetNextAvailableAchievement::handle($user, $category);

        $this->assertEquals('3 Comments Written', $achievement->name);

        $category = AchievementCategoryEnum::LESSONS_WATCHED;

        $firstLessonAchievement = Achievement::query()
            ->where('category', '=', $category)
            ->orderBy('requirement')
            ->first();

        UnlockAchievement::handle($firstLessonAchievement, $user);

        $achievement = GetNextAvailableAchievement::handle($user, $category);

        $this->assertEquals('5 Lessons Watched', $achievement->name);
    }

    /** @test */
    public function it_should_return_null_if_no_next_available_achievement_exists()
    {
        $user = User::factory()->create();

        Achievement::query()
            ->get()
            ->each(function ($achievement) use ($user) {
                UnlockAchievement::handle($achievement, $user);
            });

        $category = AchievementCategoryEnum::COMMENTS_WRITTEN;

        $achievement = GetNextAvailableAchievement::handle($user, $category);

        $this->assertNull($achievement);

        $category = AchievementCategoryEnum::LESSONS_WATCHED;

        $achievement = GetNextAvailableAchievement::handle($user, $category);

        $this->assertNull($achievement);
    }
}
