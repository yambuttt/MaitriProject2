<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // wallet / paydisini_qris / paydisini_va_mandiri / paydisini_alfamart / paydisini_indomaret
            $table->string('method');
            $table->string('provider')->nullable(); // 'wallet' / 'paydisini'

            $table->unsignedBigInteger('amount');

            // pending / paid / canceled / expired
            $table->string('status')->default('pending');

            // khusus Paydisini
            $table->string('paydisini_unique_code')->nullable()->index();
            $table->string('paydisini_pay_id')->nullable();
            $table->string('paydisini_service_id')->nullable();

            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->json('callback_payload')->nullable();

            $table->timestamp('expired_at')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_payments');
    }
};
