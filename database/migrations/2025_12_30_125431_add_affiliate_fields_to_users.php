<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_affiliate')->default(false)->after('role'); // by default NO
            $table->string('affiliate_code')->nullable()->unique()->after('is_affiliate');
            $table->foreignId('affiliate_level_id')->nullable()->after('affiliate_code')
                ->constrained('affiliate_levels')->nullOnDelete();

            $table->unsignedBigInteger('maitri_points')->default(0)->after('maitri_balance');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('affiliate_level_id');
            $table->dropColumn(['is_affiliate', 'affiliate_code', 'maitri_points']);
        });
    }
};
