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
        Schema::create('follow_up_entries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('follow_up_period_id')
                ->constrained('follow_up_periods')
                ->cascadeOnDelete();

            $table->foreignId('follow_up_item_id')
                ->constrained('follow_up_items')
                ->restrictOnDelete();

            $table->date('date')->nullable();                 // daily
            $table->unsignedTinyInteger('week_index')->nullable(); // weekly 1..5

            // NULL unknown, 0 not done, 1 done
            $table->tinyInteger('value')->nullable();

            $table->timestamps();

            // Safe uniqueness for daily + weekly:
            $table->unique(['follow_up_period_id', 'follow_up_item_id', 'date'], 'follow_up_entries_unique_daily');
            $table->unique(['follow_up_period_id', 'follow_up_item_id', 'week_index'], 'follow_up_entries_unique_weekly');

            $table->index(['follow_up_period_id', 'follow_up_item_id']);
            $table->index(['follow_up_period_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('follow_up_entries');
    }
};
