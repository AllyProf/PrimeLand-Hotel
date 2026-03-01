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
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->string('recipient');
            $table->text('message');
            $table->string('status')->default('sent'); // sent, failed, pending
            $table->string('target_type')->nullable(); // guest, staff
            $table->unsignedBigInteger('target_id')->nullable();
            $table->unsignedBigInteger('sender_id')->nullable(); // staff_id who sent it
            $table->integer('sms_count')->default(1);
            $table->text('api_response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
    }
};
