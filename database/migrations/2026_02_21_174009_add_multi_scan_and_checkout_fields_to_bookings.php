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
            // Check-In Multi-Scan
            $table->string('id_scan_back_path')->nullable()->after('id_scan_path');
            
            // Check-Out Fields
            $table->string('checkout_token', 64)->nullable()->after('checkin_token_expires_at');
            $table->dateTime('checkout_token_expires_at')->nullable()->after('checkout_token');
            $table->string('checkout_signature_path')->nullable()->after('guest_signature_path');
            $table->dateTime('checked_out_at_tablet')->nullable()->after('checked_out_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'id_scan_back_path',
                'checkout_token',
                'checkout_token_expires_at',
                'checkout_signature_path',
                'checked_out_at_tablet'
            ]);
        });
    }
};
