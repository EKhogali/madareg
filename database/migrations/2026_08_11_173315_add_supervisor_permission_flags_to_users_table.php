<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('can_assign_supervisors')->default(false)->after('role');
            $table->boolean('can_manage_supervisor_assignments')->default(false)->after('can_assign_supervisors');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['can_assign_supervisors', 'can_manage_supervisor_assignments']);
        });
    }
};
