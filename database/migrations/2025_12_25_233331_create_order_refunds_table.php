<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_refunds', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete();

            // wallet | manual_transfer
            $table->enum('refund_method', ['wallet', 'manual_transfer']);

            // jumlah refund (pakai total order)
            $table->unsignedBigInteger('amount');

            // kalau wallet refund: target user penerima saldo
            $table->foreignId('target_user_id')->nullable()->constrained('users')->nullOnDelete();

            // kalau manual: bukti transfer
            $table->string('manual_proof_path')->nullable();

            $table->string('note')->nullable();

            // 1 order hanya boleh punya 1 refund record
            $table->unique('order_id');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_refunds');
    }
};
