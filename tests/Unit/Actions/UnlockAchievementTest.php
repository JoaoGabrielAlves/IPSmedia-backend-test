<?php

namespace Tests\Unit\Actions;

use App\Actions\UnlockAchievement;
use App\Events\AchievementUnlocked;
use App\Models\Achievement;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class UnlockAchievementTest extends TestCase
{
    /** @test */
    public function it_should_unlock_achievement()
    {
        Event::fake();

        $user = User::factory()->create();

        $achievement = Achievement::query()->first();

        UnlockAchievement::handle($achievement, $user);

        Event::assertDispatched(AchievementUnlocked::class, function ($event) use ($achievement, $user) {
            return $event->achievement_name === $achievement->name
                && $event->user->is($user);
        });
    }

    /** @test */
    public function it_should_not_create_duplicate_if_achievement_already_exists()
    {
        Event::fake();

        $user = User::factory()->create();

        $achievement = Achievement::query()->first();

        UnlockAchievement::handle($achievement, $user);
        UnlockAchievement::handle($achievement, $user);

        Event::assertDispatched(AchievementUnlocked::class, function ($event) use ($achievement, $user) {
            return $event->achievement_name === $achievement->name
                && $event->user->is($user);
        });

        Event::assertDispatched(AchievementUnlocked::class, 1);
    }
}
