<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained('wallets')->cascadeOnDelete();
            $table->enum('type', ['credit','debit','hold','release']);
            $table->unsignedBigInteger('amount'); // rupiah (integer)
            $table->enum('status', ['pending','posted','reversed','failed'])->default('posted');
            $table->string('reason_code', 50)->nullable();
            $table->string('reference_id', 64)->nullable();
            $table->string('reference_type', 64)->nullable();
            $table->string('idempotency_key', 64)->unique();
            $table->json('metadata')->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();

            $table->index(['wallet_id','status','type']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('wallet_transactions');
    }
};
