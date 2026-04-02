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
        Schema::create('lost_and_found_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->nullable()->constrained('rooms')->onDelete('set null');
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->onDelete('set null');
            $table->unsignedBigInteger('staff_id'); // Housekeeper who found it
            $table->string('item_name');
            $table->text('description')->nullable();
            $table->string('location_found')->nullable();
            $table->dateTime('found_at');
            $table->string('image_path')->nullable();
            $table->enum('status', ['found', 'claimed', 'disposed', 'donated'])->default('found');
            
            // Guest info if not linked to a booking
            $table->string('guest_name')->nullable();
            $table->string('guest_phone')->nullable();
            
            // Storage and Claim info
            $table->string('storage_location')->nullable(); // e.g., Reception Locker 1
            $table->dateTime('claimed_at')->nullable();
            $table->string('claimed_by_name')->nullable();
            $table->unsignedBigInteger('processed_by_staff_id')->nullable(); // Receptionist who handled the claim
            $table->text('reception_notes')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lost_and_found_items');
    }
};
