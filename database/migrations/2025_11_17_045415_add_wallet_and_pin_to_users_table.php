<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // saldo dalam rupiah, gunakan bigInteger supaya aman angka besar
            $table->unsignedBigInteger('maitri_balance')
                ->default(0)
                ->after('password');

            // hash PIN pembayaran (bukan PIN plain text)
            $table->string('payment_pin_hash')
                ->nullable()
                ->after('maitri_balance');

            $table->timestamp('payment_pin_set_at')
                ->nullable()
                ->after('payment_pin_hash');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['maitri_balance', 'payment_pin_hash', 'payment_pin_set_at']);
        });
    }
};
