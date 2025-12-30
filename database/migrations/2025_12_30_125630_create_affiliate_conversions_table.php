<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('affiliate_conversions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('buyer_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('order_type'); // 'digiflazz' | 'marketplace'
            $table->unsignedBigInteger('order_id');

            $table->unsignedInteger('points_awarded');
            $table->timestamp('awarded_at');

            $table->timestamps();

            $table->unique(['order_type', 'order_id']); // penting: 1 order cuma sekali reward
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_conversions');
    }
};
