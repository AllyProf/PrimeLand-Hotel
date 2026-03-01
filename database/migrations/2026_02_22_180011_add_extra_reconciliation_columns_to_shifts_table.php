<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->decimal('total_mobile_expected', 15, 2)->after('total_mpesa_expected')->default(0);
            $table->decimal('total_bank_expected', 15, 2)->after('total_card_expected')->default(0);
            $table->decimal('total_online_expected', 15, 2)->after('total_bank_expected')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropColumn(['total_mobile_expected', 'total_bank_expected', 'total_online_expected']);
        });
    }
};
