<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('provider')->nullable()->after('method');
            $table->string('provider_ref_id')->nullable()->after('provider');
            $table->string('provider_status')->nullable()->after('provider_ref_id');
            $table->string('provider_message')->nullable()->after('provider_status');
            $table->string('provider_rc')->nullable()->after('provider_message');
            $table->string('provider_sn')->nullable()->after('provider_rc');
            $table->integer('provider_price')->nullable()->after('provider_sn');
            $table->json('provider_raw')->nullable()->after('provider_price');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'provider',
                'provider_ref_id',
                'provider_status',
                'provider_message',
                'provider_rc',
                'provider_sn',
                'provider_price',
                'provider_raw',
            ]);
        });
    }

};
