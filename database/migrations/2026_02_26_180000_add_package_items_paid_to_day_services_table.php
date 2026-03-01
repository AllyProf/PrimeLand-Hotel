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
        Schema::table('day_services', function (Blueprint $table) {
            $table->json('package_items_paid')->nullable()->after('package_items');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('day_services', function (Blueprint $table) {
            $table->dropColumn('package_items_paid');
        });
    }
};
