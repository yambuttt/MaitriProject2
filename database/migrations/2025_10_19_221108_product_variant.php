<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('buyer_sku_code', 120);      // kode SKU dari Digiflazz (nanti)
            $table->string('name', 180);                // nama varian (mis. 10GB Harian)
            $table->unsignedInteger('base_price');      // harga modal dari Digiflazz (Rp)
            $table->unsignedInteger('markup_rp')->nullable(); // override margin varian (Rp), null = pakai markup produk
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['product_id','buyer_sku_code']); // unik per produk
        });
    }

    public function down(): void {
        Schema::dropIfExists('product_variants');
    }
};
