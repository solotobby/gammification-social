<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payout_pool_topups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('level');
            $table->char('month', 7)->index();
            $table->decimal('amount', 15, 2);
            $table->text('note')->nullable();
            $table->uuid('admin_id')->nullable()->index();
            $table->integer('distributed_count')->default(0);
            $table->timestamps();

            $table->foreign('admin_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payout_pool_topups');
    }
};
