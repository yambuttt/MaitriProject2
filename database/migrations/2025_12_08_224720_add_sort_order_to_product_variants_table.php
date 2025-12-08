<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            // urutan manual (semakin kecil = semakin atas)
            $table->unsignedSmallInteger('sort_order')
                ->nullable()
                ->after('markup_rp');

            $table->index(['product_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropIndex(['product_id', 'sort_order']);
            $table->dropColumn('sort_order');
        });
    }
};
