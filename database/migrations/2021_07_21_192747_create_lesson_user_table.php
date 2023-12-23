<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLessonUserTable extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_user', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained();
            $table->foreignId('lesson_id')->constrained();
            $table->boolean('watched')->default(false);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_user');
    }
}
