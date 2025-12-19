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
        Schema::table('user_views', function (Blueprint $table) {
            $table->decimal('amount', 10, 6)->default(0)->after('is_paid');
            $table->uuid('poster_user_id')->nullable()->after('post_id');
            $table->index('user_id');
            $table->index('post_id');
            $table->index('poster_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_views', function (Blueprint $table) {
            $table->dropColumn('amount');
            $table->dropColumn('poster_user_id');
        });
    }
};
