<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('engagement_payout_components', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('engagement_monthly_stats_id')->index();
            $table->uuid('user_id')->index();
            $table->enum('level', ['Creator', 'Influencer', 'Basic'])->index();
            $table->char('month', 7)->index();
            $table->enum('type', ['revenue', 'bonus'])->index();
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('NGN');
            $table->text('note')->nullable();
            $table->uuid('admin_id')->nullable()->index();
            $table->uuid('payout_id')->nullable()->index();
            $table->timestamps();

            $table->foreign('engagement_monthly_stats_id')
                ->references('id')
                ->on('engagement_monthly_stats')
                ->cascadeOnDelete();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign('admin_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->foreign('payout_id')
                ->references('id')
                ->on('payouts')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('engagement_payout_components');
    }
};
