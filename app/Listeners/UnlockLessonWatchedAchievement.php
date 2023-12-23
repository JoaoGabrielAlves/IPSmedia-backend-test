<?php

namespace App\Listeners;

use App\Actions\UnlockAchievement;
use App\Models\Achievement;
use App\Models\User;

class UnlockLessonWatchedAchievement
{
    public function handle(object $event): void
    {
        /* @var User $user */
        $user = $event->user;

        $user->refresh();

        $watchedLessonsCount = $user->watched->count();

        $achievementToUnlock = Achievement::query()
            ->select('name', 'id')
            ->where('category', '=', 'Lessons watched')
            ->where('requirement', '=', $watchedLessonsCount)
            ->first();

        if ($achievementToUnlock) {
            UnlockAchievement::handle($achievementToUnlock, $user);
        }
    }
}
