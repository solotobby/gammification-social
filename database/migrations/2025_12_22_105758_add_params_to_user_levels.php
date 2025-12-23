<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_levels', function (Blueprint $table) {
            $table->string('plan_name')->nullable()->after('level_id');
            $table->string('start_date')->nullable()->after('plan_name');
            $table->dateTime('end_date')->nullable()->after('start_date');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_levels', function (Blueprint $table) {
            $table->dropColumn(['plan_name', 'start_date', 'end_date', 'status']);
        });
    }
};
