<?php

namespace App\Actions;

use App\Events\AchievementUnlocked;
use App\Models\Achievement;
use App\Models\User;

class UnlockAchievement
{
    public static function handle(Achievement $achievement, User $user): void
    {
        $achievementIsAlreadyUnlocked = $user->unlockedAchievements()
            ->where('achievement_id', '=', $achievement->id)
            ->exists();

        if (! $achievementIsAlreadyUnlocked) {
            $user->unlockedAchievements()->create([
                'achievement_id' => $achievement->id,
            ]);

            AchievementUnlocked::dispatch($achievement->name, $user);
        }
    }
}
