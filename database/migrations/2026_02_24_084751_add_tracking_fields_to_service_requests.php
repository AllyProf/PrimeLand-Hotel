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
        Schema::table('service_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('paid_to')->nullable()->after('approved_by');
            $table->unsignedBigInteger('cancelled_by')->nullable()->after('paid_to');
            
            $table->foreign('paid_to')->references('id')->on('staffs')->onDelete('set null');
            $table->foreign('cancelled_by')->references('id')->on('staffs')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropForeign(['paid_to']);
            $table->dropForeign(['cancelled_by']);
            $table->dropColumn(['paid_to', 'cancelled_by']);
        });
    }
};
