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
            $table->string('type', 32)->default('view')->change();
        });

        Schema::table('user_likes', function (Blueprint $table) {
            $table->string('type', 32)->default('like')->change();
        });

        Schema::table('user_comments', function (Blueprint $table) {
            $table->string('type', 32)->default('comment')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_views', function (Blueprint $table) {
            $table->enum('type', ['view', 'self-view'])->default('view')->change();
        });

        Schema::table('user_likes', function (Blueprint $table) {
            $table->enum('type', ['like', 'self-like'])->default('like')->change();
        });

        Schema::table('user_comments', function (Blueprint $table) {
            $table->enum('type', ['comment', 'self-comment'])->default('comment')->change();
        });
    }
};
