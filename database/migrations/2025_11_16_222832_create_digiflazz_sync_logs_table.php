<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('digiflazz_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // 'master' atau 'variant_prices'
            $table->enum('status', ['success', 'failed']);
            $table->unsignedInteger('synced_count')->default(0);
            $table->string('trigger')->default('manual'); // manual / schedule / command
            $table->text('message')->nullable();
            $table->json('context')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('digiflazz_sync_logs');
    }
};
