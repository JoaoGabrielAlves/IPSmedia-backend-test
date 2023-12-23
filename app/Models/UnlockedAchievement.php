<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\UnlockedAchievement
 *
 * @property int $id
 * @property int $user_id
 * @property int $achievement_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|UnlockedAchievement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UnlockedAchievement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UnlockedAchievement query()
 * @method static \Illuminate\Database\Eloquent\Builder|UnlockedAchievement whereAchievementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnlockedAchievement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnlockedAchievement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnlockedAchievement whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnlockedAchievement whereUserId($value)
 *
 * @mixin Eloquent
 */
class UnlockedAchievement extends Model
{
    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function achievement(): BelongsTo
    {
        return $this->belongsTo(Achievement::class);
    }
}
