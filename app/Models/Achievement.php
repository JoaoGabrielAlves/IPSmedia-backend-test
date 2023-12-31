<?php

namespace App\Models;

use App\Enums\AchievementCategoryEnum;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Achievement
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $category
 * @property int $requirement
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Achievement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Achievement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Achievement query()
 * @method static \Illuminate\Database\Eloquent\Builder|Achievement whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Achievement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Achievement whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Achievement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Achievement whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Achievement whereRequirement($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Achievement whereUpdatedAt($value)
 *
 * @property-read Collection<int, UnlockedAchievement> $unlockedAchievements
 * @property-read int|null $unlocked_achievements_count
 *
 * @mixin Eloquent
 */
class Achievement extends Model
{
    protected $guarded = [];

    protected $casts = [
        'category' => AchievementCategoryEnum::class,
    ];

    /**
     * @return HasMany<UnlockedAchievement>
     */
    public function unlockedAchievements(): HasMany
    {
        return $this->hasMany(UnlockedAchievement::class);
    }
}
