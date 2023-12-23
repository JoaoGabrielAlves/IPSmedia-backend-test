<?php

namespace Tests\Unit\Listeners;

use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
use App\Listeners\UnlockBadge;
use App\Models\Achievement;
use App\Models\Badge;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class UnlockBadgeTest extends TestCase
{
    /** @test */
    public function it_should_be_attached_to_achievement_unlocked_event()
    {
        Event::fake();

        Event::assertListening(
            AchievementUnlocked::class,
            UnlockBadge::class
        );
    }

    /** @test */
    public function it_should_change_user_badge(): void
    {
        Event::fake();

        $user = User::factory()->create();

        Badge::query()
            ->select('name', 'id', 'requirement')
            ->orderBy('requirement')
            ->each(function (Badge $badge) use ($user) {
                $end = $badge->requirement;

                if ($end === 0) {
                    $this->assertDatabaseHas('users', [
                        'id' => $user->id,
                        'badge_id' => $badge->id,
                    ]);

                    return;
                }

                for ($i = 1; $i <= $end; $i++) {
                    $user->unlockedAchievements()->create([
                        'achievement_id' => $i,
                    ]);
                }

                $achievement = Achievement::query()->first();

                $event = new AchievementUnlocked($achievement->name, $user);

                $listener = new UnlockBadge();
                $listener->handle($event);

                $user->refresh();

                $this->assertDatabaseHas('users', [
                    'id' => $user->id,
                    'badge_id' => $badge->id,
                ]);

                $user->unlockedAchievements()->delete();

                Event::assertDispatched(BadgeUnlocked::class, function (BadgeUnlocked $event) use ($badge, $user) {
                    return $event->badge_name === $badge->name && $event->user->id === $user->id;
                });
            });
    }

    /** @test */
    public function it_should_not_change_user_badge_if_no_requirement_is_met(): void
    {
        Event::fake();

        $user = User::factory()->create();

        $end = 3;

        for ($i = 1; $i <= $end; $i++) {
            $user->unlockedAchievements()->create([
                'achievement_id' => $i,
            ]);
        }

        $achievement = Achievement::query()->first();

        $event = new AchievementUnlocked($achievement->name, $user);

        $listener = new UnlockBadge();
        $listener->handle($event);

        $user->refresh();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'badge_id' => 1,
        ]);

        Event::assertNotDispatched(BadgeUnlocked::class);
    }
}
