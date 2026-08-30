<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_gifts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('sender_id');
            $table->uuid('recipient_id');
            $table->uuidMorphs('giftable');
            $table->string('artifact_id', 64);
            $table->unsignedInteger('pk_amount');
            $table->string('ref');
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['giftable_type', 'giftable_id', 'created_at']);
            $table->index(['sender_id', 'created_at']);
            $table->index(['recipient_id', 'created_at']);
            $table->index('ref');
        });

        Schema::table('community_posts', function (Blueprint $table) {
            $table->unsignedInteger('gifts_count')->default(0)->after('comments_count');
        });
    }

    public function down(): void
    {
        Schema::table('community_posts', function (Blueprint $table) {
            $table->dropColumn('gifts_count');
        });

        Schema::dropIfExists('post_gifts');
    }
};
