<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->string('code')->unique(); // kode unik transaksi

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->constrained()->cascadeOnDelete();

            $table->string('buyer_sku_code'); // untuk Digiflazz
            $table->string('target');         // nomor tujuan / user id game
            $table->string('email')->nullable(); // email bukti

            $table->enum('method', ['saldo_maitri']); // nanti bisa tambah qris, dll

            // harga
            $table->unsignedBigInteger('base_price'); // modal dari master digiflazz
            $table->unsignedBigInteger('subtotal');   // harga jual produk
            $table->unsignedBigInteger('admin_fee')->default(0);
            $table->unsignedBigInteger('total');      // subtotal + admin_fee
            $table->bigInteger('profit');             // total - base_price

            $table->enum('status', [
                'pending',    // baru dibuat, saldo sudah dipotong, belum call Digiflazz
                'processing', // sedang call Digiflazz / menunggu
                'success',
                'failed',
                'refunded',
            ])->default('pending');

            // info digiflazz
            $table->string('digiflazz_ref')->nullable();
            $table->string('digiflazz_status')->nullable();
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();

            $table->timestamp('paid_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('failed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
