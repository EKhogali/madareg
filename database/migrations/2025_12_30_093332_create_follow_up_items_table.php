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
        Schema::create('follow_up_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('follow_up_template_id')
                ->constrained('follow_up_templates')
                ->cascadeOnDelete();

            $table->string('name_ar');
            $table->string('group_ar')->nullable(); // "الصلوات الخمس" / "الأذكار" ...
            $table->unsignedTinyInteger('frequency'); // 1 daily, 2 weekly, 3 monthly
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['follow_up_template_id', 'frequency', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('follow_up_items');
    }
};
