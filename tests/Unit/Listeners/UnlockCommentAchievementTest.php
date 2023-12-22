<?php

namespace Tests\Unit\Listeners;

use App\Events\AchievementUnlocked;
use App\Events\CommentWritten;
use App\Listeners\UnlockCommentAchievement;
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
            UnlockCommentAchievement::class
        );
    }

    /** @test */
    public function it_should_unlock_achievements()
    {
        Event::fake();

        $user = User::factory()->create();

        Achievement::query()
            ->where('category', '=', 'Comments Written')
            ->orderBy('requirement')
            ->each(function (Achievement $achievement) use ($user) {
                Comment::factory()->for($user)->count($achievement->requirement)->create();

                $comment = Comment::query()->latest()->first();

                $event = new CommentWritten($comment);

                $listener = new UnlockCommentAchievement();
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
}
