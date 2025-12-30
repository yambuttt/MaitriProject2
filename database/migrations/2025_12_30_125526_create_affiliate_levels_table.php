<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('affiliate_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // contoh: Bronze, Silver, Gold
            $table->unsignedInteger('window_days')->default(30);
            $table->unsignedInteger('digiflazz_points')->default(50);
            $table->unsignedInteger('marketplace_points')->default(2000);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // default level biar langsung jalan
        DB::table('affiliate_levels')->insert([
            'name' => 'Default',
            'window_days' => 30,
            'digiflazz_points' => 50,
            'marketplace_points' => 2000,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_levels');
    }
};
