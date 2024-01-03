<?php

namespace App\Listeners;

use App\Actions\UnlockAchievement;
use App\Enums\AchievementCategoryEnum;
use App\Events\LessonWatched;
use App\Models\Achievement;

class UnlockLessonWatchedAchievement
{
    public function handle(LessonWatched $event): void
    {
        $user = $event->user;

        $user->refresh();

        $watchedLessonsCount = $user->watched->count();

        $achievementToUnlock = Achievement::query()
            ->select('name', 'id')
            ->where('category', '=', AchievementCategoryEnum::LESSONS_WATCHED)
            ->where('requirement', '=', $watchedLessonsCount)
            ->first();

        if ($achievementToUnlock) {
            UnlockAchievement::handle($achievementToUnlock, $user);
        }
    }
}
