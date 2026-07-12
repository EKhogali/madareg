<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supervisor_activity_details', function (Blueprint $table) {
            $table->unsignedTinyInteger('evaluation')
                  ->default(1)
                  ->after('activity_role');
            $table->text('notes')
                  ->nullable()
                  ->after('evaluation');
        });
    }

    public function down(): void
    {
        Schema::table('supervisor_activity_details', function (Blueprint $table) {
            $table->dropColumn(['evaluation', 'notes']);
        });
    }
};