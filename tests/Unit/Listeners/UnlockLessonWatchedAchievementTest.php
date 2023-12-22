<?php

namespace Tests\Unit\Listeners;

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
            ->where('category', '=', 'Lessons watched')
            ->orderByDesc('requirement')
            ->first();

        Lesson::factory()->count($achievementWithBiggestRequirement->requirement)->create();

        Achievement::query()
            ->select('id', 'requirement', 'name')
            ->where('category', '=', 'Lessons watched')
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
}
