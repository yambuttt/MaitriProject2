<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // debit: saldo berkurang, credit: saldo bertambah
            $table->enum('type', ['debit', 'credit']);

            $table->unsignedBigInteger('amount');          // nominal perubahan
            $table->unsignedBigInteger('balance_before');  // saldo sebelum
            $table->unsignedBigInteger('balance_after');   // saldo sesudah

            // nanti bisa diisi kalau sudah ada tabel orders/transaksi
            $table->unsignedBigInteger('order_id')->nullable();
            $table->string('ref')->nullable();             // referensi eksternal / kode unik lain

            $table->string('description')->nullable();     // contoh: "Pembelian Axis OWSEM 1GB"

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
