<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// 2025_01_01_000004_create_marketplace_order_payments_table.php
return new class extends Migration {
    public function up(): void
    {
        // database/migrations/xxxx_xx_xx_xxxxxx_create_marketplace_order_payments_table.php

        Schema::create('marketplace_order_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketplace_order_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('method'); // wallet / paydisini_xxx
            $table->unsignedBigInteger('amount');

            $table->string('status')->default('pending'); // pending, paid, canceled, expired

            $table->string('paydisini_unique_code')->nullable();
            $table->string('paydisini_pay_id')->nullable();
            $table->string('paydisini_service_id')->nullable();

            $table->json('response_payload')->nullable();  // <— TAMBAH
            $table->json('callback_payload')->nullable();  // <— SUDAH / TAMBAH
            $table->timestamp('expired_at')->nullable();   // <— TAMBAH

            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_order_payments');
    }
};
