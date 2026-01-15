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
        Schema::create('subscribers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->string('name');
            $table->string('gender', 10)->nullable();
            $table->date('birth_date')->nullable();
            $table->date('join_date')->nullable();
            $table->string('birth_place')->nullable();
            $table->string('residence_place')->nullable();
            $table->string('nationality')->nullable();
            $table->unsignedTinyInteger('gender')->nullable()->index(); // 1=Male,2=Female

            $table->foreignId('track_degree_id')->nullable()->constrained('track_degrees')->nullOnDelete();
            $table->foreignId('stage_id')->nullable()->constrained('stages')->nullOnDelete();

            // Education
            $table->string('study_level')->nullable(); // e.g., "الصف الرابع"
            $table->integer('education_type')->nullable(); // 0: public, 1: private, 2: international
            $table->string('school_name')->nullable();

            // Quran & Handwriting
            $table->boolean('is_quran_student')->default(0);
            $table->string('quran_amount')->nullable(); // e.g., "5 أجزاء"
            $table->string('quran_memorization_center')->nullable();

            // Talents
            $table->text('talents')->nullable();

            // Social Data
            $table->integer('social_status')->nullable(); // 0: living with parents, 1: orphaned father, 2: orphaned mother
            // , 3: parents divorced and living with mother, 4: parents divorced and living with father, 5: parents divorced and living with mother's grandparents
            // , 6: parents divorced and living with father's grandparents
            $table->string('father_job')->nullable();
            $table->integer('father_job_type')->nullable(); // 0: unemployed, 1: public sector, 2: private sector, 3: retired

            $table->string('mother_job')->nullable();
            $table->integer('mother_job_type')->nullable(); // 0: unemployed, 1: public sector, 2: private sector, 3: retired

            // Health
            $table->integer('health_status')->nullable(); // 0: good, 1: not good
            $table->string('disease_type')->nullable();

            // Transparency
            $table->boolean('has_relatives_at_madareg_administration')->nullable();
            $table->string('relatives_at_madareg_administration')->nullable();
            $table->boolean('has_relatives_at_madareg')->nullable();
            $table->string('relatives_at_madareg')->nullable();

            // Contact
            $table->string('father_phone')->nullable();
            $table->string('mother_phone')->nullable();


            $table->boolean('active')->default(1);
            $table->boolean('locked')->default(0);

            $table->string('image_path')->nullable();

            $table->timestamps();

            $table->foreignId('follow_up_template_id')
                ->nullable()
                ->constrained('follow_up_templates')
                ->nullOnDelete();

            $table->foreignId('group_id')
                ->nullable()
                ->constrained('groups')
                ->nullOnDelete();

            $table->index('group_id');

            $table->index(['follow_up_template_id']);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscribers');
    }
};
