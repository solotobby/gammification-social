<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('engagement_monthly_stats', function (Blueprint $table) {
            $table->boolean('amount_manual')->default(false)->after('amount');
        });
    }

    public function down(): void
    {
        Schema::table('engagement_monthly_stats', function (Blueprint $table) {
            $table->dropColumn('amount_manual');
        });
    }
};
