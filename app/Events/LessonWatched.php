<?php

namespace App\Events;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LessonWatched
{
    use Dispatchable, SerializesModels;

    public function __construct(public Lesson $lesson, public User $user)
    {

    }
}
