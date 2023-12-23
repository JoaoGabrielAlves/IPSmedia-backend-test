<?php

namespace App\Listeners;

use App\Events\BadgeUnlocked;
use App\Models\Badge;
use App\Models\User;

class UnlockBadge
{
    public function handle(object $event): void
    {
        /** @var User $User */
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
