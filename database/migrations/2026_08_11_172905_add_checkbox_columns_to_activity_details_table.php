<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_details', function (Blueprint $table) {
            $table->boolean('att_present')->default(false)->after('subscriber_id');
            $table->boolean('att_on_time')->default(false)->after('att_present');
            $table->boolean('att_interaction')->default(false)->after('att_on_time');
            $table->boolean('att_subscription')->default(false)->after('att_interaction');
        });
    }

    public function down(): void
    {
        Schema::table('activity_details', function (Blueprint $table) {
            $table->dropColumn(['att_present', 'att_on_time', 'att_interaction', 'att_subscription']);
        });
    }
};
