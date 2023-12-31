<?php

namespace App\Listeners;

use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
use App\Models\Badge;

class UnlockBadge
{
    public function handle(AchievementUnlocked $event): void
    {
        $user = $event->user;

        $unlockedAchievementCount = $user->unlockedAchievements()->count();

        $badgeToUnlock = Badge::query()
            ->select('id', 'name')
            ->where('requirement', '=', $unlockedAchievementCount)
            ->first();

        if ($badgeToUnlock) {
            $user->badge_id = $badgeToUnlock->id;
            $user->save();

            BadgeUnlocked::dispatch($badgeToUnlock->name, $user);
        }
    }
}
