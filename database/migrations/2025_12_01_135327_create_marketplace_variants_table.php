<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// 2025_01_01_000002_create_marketplace_variants_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('marketplace_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketplace_product_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->string('name'); // contoh: 30 Hari
            $table->unsignedInteger('duration_days')->nullable(); // 30/60/90
            $table->unsignedBigInteger('price'); // dalam rupiah
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_variants');
    }
};

