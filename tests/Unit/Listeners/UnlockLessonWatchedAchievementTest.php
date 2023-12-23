<?php

namespace Tests\Unit\Listeners;

use App\Enums\AchievementCategoryEnum;
use App\Events\AchievementUnlocked;
use App\Events\LessonWatched;
use App\Listeners\UnlockLessonWatchedAchievement;
use App\Models\Achievement;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class UnlockLessonWatchedAchievementTest extends TestCase
{
    /** @test */
    public function it_should_be_attached_to_lesson_watched_event()
    {
        Event::fake();

        Event::assertListening(
            LessonWatched::class,
            UnlockLessonWatchedAchievement::class
        );
    }

    /** @test */
    public function it_should_unlock_achievements()
    {
        Event::fake();

        $user = User::factory()->create();

        $achievementWithBiggestRequirement = Achievement::query()
            ->select('id', 'requirement', 'name')
            ->where('category', '=', AchievementCategoryEnum::LESSONS_WATCHED)
            ->orderByDesc('requirement')
            ->first();

        Lesson::factory()->count($achievementWithBiggestRequirement->requirement)->create();

        Achievement::query()
            ->select('id', 'requirement', 'name')
            ->where('category', '=', AchievementCategoryEnum::LESSONS_WATCHED)
            ->orderBy('requirement')
            ->each(function (Achievement $achievement) use ($user) {
                $lessons = Lesson::query()
                    ->select('id')
                    ->limit($achievement->requirement)
                    ->get()
                    ->pluck('id')
                    ->mapWithKeys(function (int $id) {
                        return [$id => ['watched' => true]];
                    })
                    ->toArray();

                $user->lessons()->attach($lessons);

                $lesson = $user->watched()->latest()->first();

                $event = new LessonWatched($lesson, $user);

                $listener = new UnlockLessonWatchedAchievement();
                $listener->handle($event);

                $this->assertDatabaseHas('unlocked_achievements', [
                    'user_id' => $user->id,
                    'achievement_id' => $achievement->id,
                ]);

                $user->lessons()->detach();

                Event::assertDispatched(AchievementUnlocked::class, function (AchievementUnlocked $event) use ($achievement, $user) {
                    return $event->achievementName === $achievement->name && $event->user->is($user);
                });
            });
    }

    /** @test */
    public function it_should_not_unlock_achievement_if_requirement_is_not_met()
    {
        Event::fake();

        $user = User::factory()->create();

        $lessons = Lesson::query()
            ->select('id')
            ->limit(2)
            ->get()
            ->pluck('id')
            ->mapWithKeys(function (int $id) {
                return [$id => ['watched' => true]];
            })
            ->toArray();

        $user->lessons()->attach($lessons);

        $lesson = $user->watched()->latest()->first();

        $event = new LessonWatched($lesson, $user);

        $listener = new UnlockLessonWatchedAchievement();
        $listener->handle($event);

        $this->assertDatabaseEmpty('unlocked_achievements');

        Event::assertNotDispatched(AchievementUnlocked::class);
    }

    /** @test */
    public function it_should_not_create_an_duplicate_if_achievement_is_already_unlocked()
    {
        Event::fake();

        $user = User::factory()->create();

        $lesson = Lesson::query()->first();

        $user->lessons()->attach($lesson, ['watched' => true]);

        $event = new LessonWatched($lesson, $user);

        $listener = new UnlockLessonWatchedAchievement();
        $listener->handle($event);

        $listener = new UnlockLessonWatchedAchievement();
        $listener->handle($event);

        $achievement = Achievement::query()
            ->select('id', 'requirement', 'name')
            ->where('category', '=', AchievementCategoryEnum::LESSONS_WATCHED)
            ->orderBy('requirement')
            ->first();

        $this->assertDatabaseCount('unlocked_achievements', 1);

        Event::assertDispatched(AchievementUnlocked::class, 1);

        Event::assertDispatched(AchievementUnlocked::class, function (AchievementUnlocked $event) use ($achievement, $user) {
            return $event->achievementName === $achievement->name && $event->user->is($user);
        });
    }

    /** @test */
    public function it_should_not_unlock_achievement_if_lesson_is_not_watched()
    {
        Event::fake();

        $user = User::factory()->create();

        $lesson = Lesson::query()->first();

        $user->lessons()->attach($lesson);

        $event = new LessonWatched($lesson, $user);

        $listener = new UnlockLessonWatchedAchievement();
        $listener->handle($event);

        $this->assertDatabaseEmpty('unlocked_achievements');

        Event::assertNotDispatched(AchievementUnlocked::class);
    }
}
