<?php

namespace App\Http\Controllers;

use App\Actions\GetNextAvailableAchievement;
use App\Enums\AchievementCategoryEnum;
use App\Models\Badge;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AchievementsController extends Controller
{
    public function index(User $user): JsonResponse
    {
        $unlockedAchievements = $user
            ->unlockedAchievements
            ->pluck('achievement.name')
            ->toArray();

        $nextAvailableCommentAchievement = GetNextAvailableAchievement::handle(
            $user,
            AchievementCategoryEnum::COMMENTS_WRITTEN
        );

        $nextAvailableLessonWatchedAchievement = GetNextAvailableAchievement::handle(
            $user,
            AchievementCategoryEnum::LESSONS_WATCHED
        );

        $currentBadge = $user->badge;

        $currentBadgeRequirement = $currentBadge?->requirement;

        $nextBadge = Badge::query()
            ->where('requirement', '>', $currentBadgeRequirement)
            ->orderBy('requirement')
            ->first();

        $remainingToUnlockNextBadge = $nextBadge
            ? $nextBadge->requirement - $currentBadgeRequirement
            : null;

        return response()->json([
            'unlocked_achievements' => $unlockedAchievements,
            'next_available_achievements' => array_values(array_filter([
                $nextAvailableCommentAchievement?->name,
                $nextAvailableLessonWatchedAchievement?->name,
            ])),
            'current_badge' => $user->badge?->name,
            'next_badge' => $nextBadge?->name,
            'remaining_to_unlock_next_badge' => $remainingToUnlockNextBadge,
        ]);
    }
}
