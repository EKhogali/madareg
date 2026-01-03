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
        Schema::create('follow_up_periods', function (Blueprint $table) {
            $table->id();

            $table->foreignId('follow_up_template_id')
                ->constrained('follow_up_templates')
                ->restrictOnDelete();

            $table->foreignId('subscriber_id')
                ->constrained('subscribers')
                ->cascadeOnDelete();

            // The user who fills the sheet (normally subscriber.user_id)
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month'); // 1..12

            $table->boolean('is_month_locked')->default(false);
            $table->timestamp('month_locked_at')->nullable();
            $table->foreignId('month_locked_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->unique(['subscriber_id', 'year', 'month'], 'follow_up_period_unique_month');
            $table->index(['follow_up_template_id', 'year', 'month']);
            $table->index(['user_id', 'year', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('follow_up_periods');
    }
};
