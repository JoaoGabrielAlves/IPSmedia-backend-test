<?php

namespace App\Actions;

use App\Enums\AchievementCategoryEnum;
use App\Models\Achievement;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class GetNextAvailableAchievement
{
    public static function handle(User $user, AchievementCategoryEnum $category): ?Achievement
    {
        return Achievement::query()
            ->select('name')
            ->where('category', '=', $category)
            ->whereDoesntHave('unlockedAchievements', function (Builder $query) use ($user) {
                $query->where('user_id', '=', $user->id);
            })
            ->orderBy('requirement')
            ->first();
    }
}
