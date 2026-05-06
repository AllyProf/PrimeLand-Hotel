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
        // 1. day_services
        Schema::table('day_services', function (Blueprint $table) {
            $table->decimal('amount', 15, 2)->change();
            $table->decimal('amount_paid', 15, 2)->nullable()->change();
            $table->decimal('discount_amount', 15, 2)->default(0)->change();
        });

        // 2. stock_transfers
        Schema::table('stock_transfers', function (Blueprint $table) {
            $table->decimal('unit_cost', 15, 2)->nullable()->change();
            $table->decimal('total_cost', 15, 2)->nullable()->change();
            $table->decimal('selling_price_per_pic', 15, 2)->nullable()->change();
            $table->decimal('selling_price_per_serving', 15, 2)->nullable()->change();
            $table->decimal('expected_revenue_pic_sale', 15, 2)->nullable()->change();
            $table->decimal('expected_revenue_serving_sale', 15, 2)->nullable()->change();
            $table->decimal('expected_profit_pic_sale', 15, 2)->nullable()->change();
            $table->decimal('expected_profit_serving_sale', 15, 2)->nullable()->change();
        });

        // 3. stock_receipts
        Schema::table('stock_receipts', function (Blueprint $table) {
            $table->decimal('buying_price_per_bottle', 15, 2)->change();
            $table->decimal('selling_price_per_bottle', 15, 2)->change();
            $table->decimal('discount_amount', 15, 2)->nullable()->change();
        });

        // 4. shopping_lists
        Schema::table('shopping_lists', function (Blueprint $table) {
            $table->decimal('total_estimated_cost', 15, 2)->nullable()->change();
            $table->decimal('total_actual_cost', 15, 2)->nullable()->change();
            $table->decimal('budget_amount', 15, 2)->nullable()->change();
            $table->decimal('amount_used', 15, 2)->default(0)->change();
            $table->decimal('amount_remaining', 15, 2)->nullable()->change();
        });

        // 5. shopping_list_items
        Schema::table('shopping_list_items', function (Blueprint $table) {
            $table->decimal('estimated_price', 15, 2)->nullable()->change();
            $table->decimal('purchased_cost', 15, 2)->nullable()->change();
            $table->decimal('unit_price', 15, 2)->nullable()->change();
        });

        // 6. service_catalog
        Schema::table('service_catalog', function (Blueprint $table) {
            $table->decimal('price_tanzanian', 15, 2)->default(0)->change();
            $table->decimal('price_international', 15, 2)->nullable()->change();
            $table->decimal('child_price_tanzanian', 15, 2)->nullable()->change();
        });

        // 7. recipes
        Schema::table('recipes', function (Blueprint $table) {
            $table->decimal('selling_price', 15, 2)->default(0)->change();
        });

        // 8. product_variants
        Schema::table('product_variants', function (Blueprint $table) {
            $table->decimal('selling_price_per_pic', 15, 2)->nullable()->change();
            $table->decimal('selling_price_per_serving', 15, 2)->nullable()->change();
        });

        // 9. service_requests
        Schema::table('service_requests', function (Blueprint $table) {
            $table->decimal('unit_price_tsh', 15, 2)->change();
            $table->decimal('total_price_tsh', 15, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('day_services', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->change();
            $table->decimal('amount_paid', 10, 2)->nullable()->change();
            $table->decimal('discount_amount', 10, 2)->default(0)->change();
        });

        Schema::table('stock_transfers', function (Blueprint $table) {
            $table->decimal('unit_cost', 10, 2)->nullable()->change();
            $table->decimal('total_cost', 10, 2)->nullable()->change();
            $table->decimal('selling_price_per_pic', 10, 2)->nullable()->change();
            $table->decimal('selling_price_per_serving', 10, 2)->nullable()->change();
            $table->decimal('expected_revenue_pic_sale', 10, 2)->nullable()->change();
            $table->decimal('expected_revenue_serving_sale', 10, 2)->nullable()->change();
            $table->decimal('expected_profit_pic_sale', 10, 2)->nullable()->change();
            $table->decimal('expected_profit_serving_sale', 10, 2)->nullable()->change();
        });

        Schema::table('stock_receipts', function (Blueprint $table) {
            $table->decimal('buying_price_per_bottle', 10, 2)->change();
            $table->decimal('selling_price_per_bottle', 10, 2)->change();
            $table->decimal('discount_amount', 10, 2)->nullable()->change();
        });

        Schema::table('shopping_lists', function (Blueprint $table) {
            $table->decimal('total_estimated_cost', 10, 2)->nullable()->change();
            $table->decimal('total_actual_cost', 10, 2)->nullable()->change();
            $table->decimal('budget_amount', 10, 2)->nullable()->change();
            $table->decimal('amount_used', 10, 2)->default(0)->change();
            $table->decimal('amount_remaining', 10, 2)->nullable()->change();
        });

        Schema::table('shopping_list_items', function (Blueprint $table) {
            $table->decimal('estimated_price', 10, 2)->nullable()->change();
            $table->decimal('purchased_cost', 10, 2)->nullable()->change();
            $table->decimal('unit_price', 10, 2)->nullable()->change();
        });

        Schema::table('service_catalog', function (Blueprint $table) {
            $table->decimal('price_tanzanian', 10, 2)->default(0)->change();
            $table->decimal('price_international', 10, 2)->nullable()->change();
            $table->decimal('child_price_tanzanian', 10, 2)->nullable()->change();
        });

        Schema::table('recipes', function (Blueprint $table) {
            $table->decimal('selling_price', 10, 2)->default(0)->change();
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->decimal('selling_price_per_pic', 10, 2)->nullable()->change();
            $table->decimal('selling_price_per_serving', 10, 2)->nullable()->change();
        });

        Schema::table('service_requests', function (Blueprint $table) {
            $table->decimal('unit_price_tsh', 10, 2)->change();
            $table->decimal('total_price_tsh', 10, 2)->change();
        });
    }
};
