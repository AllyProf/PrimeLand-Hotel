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
        Schema::table('feedbacks', function (Blueprint $table) {
            // Drop the existing foreign key constraint first
            $table->dropForeign(['booking_id']);

            // Modify the column to be nullable
            $table->unsignedBigInteger('booking_id')->nullable()->change();

            // Re-add the foreign key constraint allowing null values
            $table->foreign('booking_id')
                  ->references('id')
                  ->on('bookings')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feedbacks', function (Blueprint $table) {
            $table->dropForeign(['booking_id']);
            $table->unsignedBigInteger('booking_id')->nullable(false)->change();
            $table->foreign('booking_id')
                  ->references('id')
                  ->on('bookings')
                  ->onDelete('cascade');
        });
    }
};
