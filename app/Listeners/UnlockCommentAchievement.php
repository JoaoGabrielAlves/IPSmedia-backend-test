<?php

namespace App\Listeners;

use App\Events\AchievementUnlocked;
use App\Models\Achievement;
use App\Models\Comment;

class UnlockCommentAchievement
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

        $user->unlockedAchievements()->create([
            'achievement_id' => $achievementToUnlock->id,
        ]);

        if ($achievementToUnlock->exists()) {
            AchievementUnlocked::dispatch($achievementToUnlock->name, $user);
        }
    }
}
