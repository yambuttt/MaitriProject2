<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// 2025_01_01_000003_create_marketplace_orders_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('marketplace_orders', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique(); // contoh: MPM-00001

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->foreignId('marketplace_product_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('marketplace_variant_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->string('customer_email');
            $table->string('customer_phone')->nullable();

            $table->unsignedBigInteger('price'); // harga variant saat order
            $table->unsignedBigInteger('fee')->default(0); // kalau ada
            $table->unsignedBigInteger('total_amount'); // price + fee

            $table->string('payment_method'); // wallet, paydisini_qris, dll
            $table->string('payment_status')->default('not_paid'); // not_paid, pending, paid

            // status internal yang kamu mau
            $table->string('status')->default('not_paid'); 
            // not_paid
            // paid_received
            // paid_processing
            // paid_rejected
            // paid_finished

            $table->text('user_note')->nullable(); // note dari user di checkout
            $table->text('admin_note')->nullable(); // berisi akun yang dikirim
            $table->foreignId('processed_by_admin_id')->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamp('paid_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('finished_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_orders');
    }
};
