<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) drop foreign key dulu
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['subcategory_id']);
        });

        // 2) ubah kolom jadi nullable (pakai raw SQL biar nggak perlu doctrine/dbal)
        DB::statement('ALTER TABLE products MODIFY subcategory_id BIGINT UNSIGNED NULL');

        // 3) tambahkan lagi foreign key, tapi sekarang boleh NULL dan kalau subcategory dihapus → set null
        Schema::table('products', function (Blueprint $table) {
            $table->foreign('subcategory_id')
                ->references('id')
                ->on('subcategories')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        // balik lagi jadi NOT NULL kalau di-rollback
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['subcategory_id']);
        });

        DB::statement('ALTER TABLE products MODIFY subcategory_id BIGINT UNSIGNED NOT NULL');

        Schema::table('products', function (Blueprint $table) {
            $table->foreign('subcategory_id')
                ->references('id')
                ->on('subcategories')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }
};
