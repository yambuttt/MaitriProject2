<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('point_redemptions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // wallet = jadi saldo maitri (instan), cash = minta transfer manual (pending)
            $table->enum('method', ['wallet', 'cash']);

            // points yang diminta user
            $table->unsignedBigInteger('points');

            // nominal rupiah (default 1 point = 1 rupiah, bisa kamu ubah nanti)
            $table->unsignedBigInteger('amount');

            $table->enum('status', ['instant', 'pending', 'approved', 'rejected'])->default('pending');

            // untuk admin hubungi manual via WA
            $table->string('phone')->nullable();

            // admin evidence
            $table->string('proof_path')->nullable();
            $table->text('admin_note')->nullable();

            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_redemptions');
    }
};
