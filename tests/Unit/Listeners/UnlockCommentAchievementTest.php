<?php

namespace Tests\Unit\Listeners;

use App\Events\AchievementUnlocked;
use App\Events\CommentWritten;
use App\Listeners\UnlockCommentWrittenAchievement;
use App\Models\Achievement;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class UnlockCommentAchievementTest extends TestCase
{
    /** @test */
    public function it_should_be_attached_to_comment_written_event(): void
    {
        Event::fake();

        Event::assertListening(
            CommentWritten::class,
            UnlockCommentWrittenAchievement::class
        );
    }

    /** @test */
    public function it_should_unlock_achievements()
    {
        Event::fake();

        $user = User::factory()->create();

        Achievement::query()
            ->select('id', 'requirement', 'name')
            ->where('category', '=', 'Comments Written')
            ->orderBy('requirement')
            ->each(function (Achievement $achievement) use ($user) {
                Comment::factory()->for($user)->count($achievement->requirement)->create();

                $comment = Comment::query()->latest()->first();

                $event = new CommentWritten($comment);

                $listener = new UnlockCommentWrittenAchievement();
                $listener->handle($event);

                $this->assertDatabaseHas('unlocked_achievements', [
                    'user_id' => $user->id,
                    'achievement_id' => $achievement->id,
                ]);

                Comment::query()->delete();

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

        Comment::factory()->for($user)->count(2)->create();

        $comment = Comment::query()->first();

        $event = new CommentWritten($comment);

        $listener = new UnlockCommentWrittenAchievement();
        $listener->handle($event);

        $this->assertDatabaseEmpty('unlocked_achievements');

        Event::assertNotDispatched(AchievementUnlocked::class);
    }

    /** @test */
    public function it_should_not_create_an_duplicate_if_achievement_is_already_unlocked()
    {
        Event::fake();

        $user = User::factory()->create();

        Comment::factory()->for($user)->create();

        $comment = Comment::query()->first();

        $event = new CommentWritten($comment);

        $listener = new UnlockCommentWrittenAchievement();
        $listener->handle($event);

        $listener = new UnlockCommentWrittenAchievement();
        $listener->handle($event);

        $achievement = Achievement::query()
            ->select('id', 'requirement', 'name')
            ->where('category', '=', 'Comments Written')
            ->orderBy('requirement')
            ->first();

        $this->assertDatabaseCount('unlocked_achievements', 1);

        Event::assertDispatched(AchievementUnlocked::class, 1);

        Event::assertDispatched(AchievementUnlocked::class, function (AchievementUnlocked $event) use ($achievement, $user) {
            return $event->achievementName === $achievement->name && $event->user->is($user);
        });
    }
}
