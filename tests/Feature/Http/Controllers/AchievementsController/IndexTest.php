<?php

namespace Tests\Feature\Http\Controllers\AchievementsController;

use App\Actions\UnlockAchievement;
use App\Enums\AchievementCategoryEnum;
use App\Models\Achievement;
use App\Models\Badge;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class IndexTest extends TestCase
{
    /** @test */
    public function it_should_return_expected_data_with_all_data()
    {
        $user = User::factory()->create();

        $commentAchievement = Achievement::query()
            ->where('category', '=', AchievementCategoryEnum::COMMENTS_WRITTEN)
            ->first();

        $lessonWatchedAchievement = Achievement::query()
            ->where('category', '=', AchievementCategoryEnum::LESSONS_WATCHED)
            ->first();

        UnlockAchievement::handle($commentAchievement, $user);
        UnlockAchievement::handle($lessonWatchedAchievement, $user);

        $this->actingAs($user)
            ->get(route('achievements.index', $user))
            ->assertExactJson([
                'current_badge' => $user->badge->name,
                'unlocked_achievements' => [
                    'First Lesson Watched',
                    'First Comment Written',
                ],
                'next_available_achievements' => [
                    '3 Comments Written',
                    '5 Lessons Watched',
                ],
                'next_badge' => '4 Achievements',
                'remaining_to_unlock_next_badge' => 4,
            ]);
    }

    /** @test */
    public function it_should_return_empty_unlocked_achievements_if_not_achievements_unlocked()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('achievements.index', $user))
            ->assertExactJson([
                'current_badge' => $user->badge->name,
                'unlocked_achievements' => [],
                'next_available_achievements' => [
                    'First Comment Written',
                    'First Lesson Watched',
                ],
                'next_badge' => '4 Achievements',
                'remaining_to_unlock_next_badge' => 4,
            ]);
    }

    /** @test */
    public function it_should_return_empty_next_available_achievements_if_there_is_no_remaining_achievements_to_unlock()
    {
        Event::fake();

        $user = User::factory()->create();

        Achievement::query()
            ->get()
            ->each(function ($achievement) use ($user) {
                UnlockAchievement::handle($achievement, $user);
            });

        $this->actingAs($user)
            ->get(route('achievements.index', $user))
            ->assertExactJson([
                'current_badge' => $user->badge->name,
                'unlocked_achievements' => [
                    'First Lesson Watched',
                    '5 Lessons Watched',
                    '10 Lessons Watched',
                    '25 Lessons Watched',
                    '50 Lessons Watched',
                    'First Comment Written',
                    '3 Comments Written',
                    '5 Comments Written',
                    '10 Comments Written',
                    '20 Comments Written',
                ],
                'next_available_achievements' => [],
                'next_badge' => '4 Achievements',
                'remaining_to_unlock_next_badge' => 4,
            ]);
    }

    /** @test */
    public function it_should_empty_next_badge_and_remaining_to_unlock_next_badge_if_there_is_no_next_badge_to_unlock()
    {
        $user = User::factory()->create();

        $badgeWithBiggestRequirement = Badge::query()
            ->orderByDesc('requirement')
            ->first();

        $user->badge_id = $badgeWithBiggestRequirement->id;
        $user->save();

        $this->actingAs($user)
            ->get(route('achievements.index', $user))
            ->assertExactJson([
                'current_badge' => $user->badge->name,
                'unlocked_achievements' => [],
                'next_available_achievements' => [
                    'First Comment Written',
                    'First Lesson Watched',
                ],
                'next_badge' => null,
                'remaining_to_unlock_next_badge' => null,
            ]);
    }
}
