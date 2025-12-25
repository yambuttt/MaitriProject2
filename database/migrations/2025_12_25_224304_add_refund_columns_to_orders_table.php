<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('refunded_at')->nullable()->after('failed_at');
            $table->unsignedBigInteger('refund_amount')->nullable()->after('refunded_at');
            $table->string('refund_reason')->nullable()->after('refund_amount');

            // siapa user yang menerima refund (kalau guest, tetap null)
            $table->foreignId('refunded_to_user_id')
                ->nullable()
                ->after('refund_reason')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['refunded_to_user_id']);
            $table->dropColumn(['refunded_at', 'refund_amount', 'refund_reason', 'refunded_to_user_id']);
        });
    }
};
