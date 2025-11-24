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
        Schema::create('stage_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stage_id')->constrained()->cascadeOnDelete();
            $table->string('category'); //['معارف', 'قيم', 'مهارات']
            $table->string('name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stage_topics');
    }
};
