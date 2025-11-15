<?php

// database/migrations/XXXX_XX_XX_XXXXXX_add_digiflazz_variant_id_to_product_variants.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            // nullable karena tidak semua varian wajib Digiflazz
            $table->foreignId('digiflazz_variant_id')
                ->nullable()
                ->constrained('digiflazz_variants')
                ->nullOnDelete();

            // kalau mau, bisa tambah index di buyer_sku_code juga
            // $table->index('buyer_sku_code');
        });
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropForeign(['digiflazz_variant_id']);
            $table->dropColumn('digiflazz_variant_id');
        });
    }
};
