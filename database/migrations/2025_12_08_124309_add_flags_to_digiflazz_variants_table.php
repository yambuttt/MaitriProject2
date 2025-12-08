<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('digiflazz_variants', function (Blueprint $table) {
            $table->boolean('buyer_product_status')
                ->default(true)
                ->after('status');

            $table->boolean('seller_product_status')
                ->default(true)
                ->after('buyer_product_status');
        });
    }

    public function down(): void
    {
        Schema::table('digiflazz_variants', function (Blueprint $table) {
            $table->dropColumn(['buyer_product_status', 'seller_product_status']);
        });
    }
};
