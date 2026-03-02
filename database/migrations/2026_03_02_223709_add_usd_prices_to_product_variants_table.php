<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->decimal('selling_price_per_serving_usd', 8, 2)->nullable()->after('selling_price_per_serving');
            $table->decimal('selling_price_per_pic_usd', 8, 2)->nullable()->after('selling_price_per_pic');
        });
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['selling_price_per_serving_usd', 'selling_price_per_pic_usd']);
        });
    }
};
