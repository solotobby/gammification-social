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
        Schema::table('posts', function (Blueprint $table) {
            if (! Schema::hasColumn('posts', 'is_monetized')) {
                $table->boolean('is_monetized')->default(true)->after('monetization_paused')->index();
            }
            if (! Schema::hasColumn('posts', 'monetization_note')) {
                $table->string('monetization_note', 255)->nullable()->after('is_monetized');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $drops = [];
            if (Schema::hasColumn('posts', 'monetization_note')) {
                $drops[] = 'monetization_note';
            }
            if (Schema::hasColumn('posts', 'is_monetized')) {
                $drops[] = 'is_monetized';
            }
            if (! empty($drops)) {
                $table->dropColumn($drops);
            }
        });
    }
};
