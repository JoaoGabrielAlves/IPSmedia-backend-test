<?php

namespace App\Listeners;

use App\Events\AchievementUnlocked;
use App\Models\Achievement;
use App\Models\User;

class UnlockLessonWatchedAchievement
{
    public function __construct()
    {

    }

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
            $user->unlockedAchievements()->create([
                'achievement_id' => $achievementToUnlock->id,
            ]);

            AchievementUnlocked::dispatch($achievementToUnlock->name, $user);
        }
    }
}
