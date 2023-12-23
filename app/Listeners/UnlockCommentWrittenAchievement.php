<?php

namespace App\Listeners;

use App\Events\AchievementUnlocked;
use App\Models\Achievement;
use App\Models\Comment;

class UnlockCommentWrittenAchievement
{
    public function __construct()
    {

    }

    public function handle(object $event): void
    {
        /* @var Comment $comment */
        $comment = $event->comment;

        $user = $comment->user;

        $userCommentsCount = $user->comments()->count();

        $achievementToUnlock = Achievement::query()
            ->select('name', 'id')
            ->where('category', '=', 'Comments Written')
            ->where('requirement', '=', $userCommentsCount)
            ->first();

        if ($achievementToUnlock) {
            $user->unlockedAchievements()->create([
                'achievement_id' => $achievementToUnlock->id,
            ]);

            AchievementUnlocked::dispatch($achievementToUnlock->name, $user);
        }
    }
}
