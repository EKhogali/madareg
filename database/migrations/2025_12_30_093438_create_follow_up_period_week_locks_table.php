<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('follow_up_period_week_locks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('follow_up_period_id')
                ->constrained('follow_up_periods')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('week_index'); // 1..5
            $table->boolean('is_locked')->default(false);
            $table->timestamp('locked_at')->nullable();
            $table->foreignId('locked_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->unique(['follow_up_period_id', 'week_index'], 'follow_up_week_lock_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('follow_up_period_week_locks');
    }
};
