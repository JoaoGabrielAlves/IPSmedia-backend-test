<?php

namespace App\Listeners;

use App\Actions\UnlockAchievement;
use App\Models\Achievement;
use App\Models\Comment;

class UnlockCommentWrittenAchievement
{
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
            UnlockAchievement::handle($achievementToUnlock, $user);
        }
    }
}
