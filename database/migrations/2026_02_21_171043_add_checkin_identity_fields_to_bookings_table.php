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
            $table->string('id_document_type')->nullable();
            $table->string('id_document_number')->nullable();
            $table->string('id_scan_path')->nullable();
            $table->string('guest_signature_path')->nullable();
            $table->timestamp('identity_captured_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'id_document_type',
                'id_document_number',
                'id_scan_path',
                'guest_signature_path',
                'identity_captured_at'
            ]);
        });
    }
};
