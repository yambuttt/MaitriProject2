<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            // Hapus kolom base_price
            if (Schema::hasColumn('product_variants', 'base_price')) {
                $table->dropColumn('base_price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            // Kalau di-rollback, tambahkan lagi (sesuaikan tipe dengan migration awalmu)
            $table->unsignedBigInteger('base_price')->default(0);
        });
    }
};
