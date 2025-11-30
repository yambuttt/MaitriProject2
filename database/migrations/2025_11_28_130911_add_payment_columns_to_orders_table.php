<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // metode pembayaran utama order: wallet / paydisini
            $table->string('payment_method')
                ->default('wallet')
                ->after('total');

            // status pembayaran: pending / paid / canceled / expired
            $table->string('payment_status')
                ->default('paid')
                ->after('payment_method');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'payment_status']);
        });
    }
};
