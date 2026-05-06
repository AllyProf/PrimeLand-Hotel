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
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('total_price', 15, 2)->change();
            $table->decimal('amount_paid', 15, 2)->nullable()->change();
            $table->decimal('total_service_charges_tsh', 15, 2)->change();
            $table->decimal('total_bill_tsh', 15, 2)->nullable()->change();
            $table->decimal('recommended_price', 15, 2)->nullable()->change();
            $table->decimal('cancellation_fee', 15, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('total_price', 10, 2)->change();
            $table->decimal('amount_paid', 10, 2)->change();
            $table->decimal('total_service_charges_tsh', 10, 2)->change();
            $table->decimal('total_bill_tsh', 10, 2)->change();
            $table->decimal('recommended_price', 10, 2)->change();
            $table->decimal('cancellation_fee', 10, 2)->change();
        });
    }
};
