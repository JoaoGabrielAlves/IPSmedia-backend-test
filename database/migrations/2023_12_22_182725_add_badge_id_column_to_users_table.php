<?php

use App\Models\Badge;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignIdFor(Badge::class)->default(1)->after('id');

            $table->index('badge_id', 'users_badge_id_index');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeignIdFor(Badge::class);

            $table->dropIndex('users_badge_id_index');
        });
    }
};
