<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            // Display-only USD price — system logic still uses selling_price (TSH)
            $table->decimal('selling_price_usd', 8, 2)->nullable()->after('selling_price');
        });
    }

    public function down(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->dropColumn('selling_price_usd');
        });
    }
};
