<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('currency', 3)->default('IDR');
            $table->unsignedBigInteger('available_balance')->default(0);
            $table->unsignedBigInteger('pending_balance')->default(0);
            $table->boolean('is_frozen')->default(false);
            $table->string('frozen_reason', 255)->nullable();
            $table->timestamps();

            $table->index(['is_frozen']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('wallets');
    }
};
