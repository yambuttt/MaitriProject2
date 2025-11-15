<?php

// database/migrations/XXXX_XX_XX_XXXXXX_create_digiflazz_variants_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('digiflazz_variants', function (Blueprint $table) {
            $table->id();

            $table->string('buyer_sku_code')->unique();
            $table->string('product_name');
            $table->string('brand')->nullable();
            $table->string('category')->nullable();

            // harga beli kita ke Digiflazz (dalam rupiah, integer)
            $table->unsignedInteger('base_price');

            // misal: active, gangguan, nonaktif (ikuti value Digiflazz)
            $table->string('status')->nullable();

            // kapan row ini terakhir di-update dari API Digiflazz
            $table->timestamp('last_synced_at')->nullable();

            // opsional: simpan raw json dari Digiflazz untuk referensi
            $table->json('raw')->nullable();

            $table->timestamps();

            $table->index('brand');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('digiflazz_variants');
    }
};
