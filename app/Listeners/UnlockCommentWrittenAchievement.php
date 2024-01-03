<?php

namespace App\Listeners;

use App\Actions\UnlockAchievement;
use App\Enums\AchievementCategoryEnum;
use App\Events\CommentWritten;
use App\Models\Achievement;

class UnlockCommentWrittenAchievement
{
    public function handle(CommentWritten $event): void
    {
        $comment = $event->comment;

        $user = $comment->user;

        $userCommentsCount = $user->comments()->count();

        $achievementToUnlock = Achievement::query()
            ->select('name', 'id')
            ->where('category', '=', AchievementCategoryEnum::COMMENTS_WRITTEN)
            ->where('requirement', '=', $userCommentsCount)
            ->first();

        if ($achievementToUnlock) {
            UnlockAchievement::handle($achievementToUnlock, $user);
        }
    }
}
