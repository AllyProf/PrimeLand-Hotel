<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('rate_source')->nullable()->after('locked_exchange_rate')
                  ->comment('Source of exchange rate: system, manual, booking.com, bank, paypal, other');
            $table->string('exchange_rate_note')->nullable()->after('rate_source')
                  ->comment('Staff note explaining why rate was overridden');
            $table->string('exchange_rate_overridden_by')->nullable()->after('exchange_rate_note')
                  ->comment('Name or ID of staff who overrode the rate');
            $table->timestamp('exchange_rate_overridden_at')->nullable()->after('exchange_rate_overridden_by')
                  ->comment('When the rate was overridden');
            $table->decimal('original_exchange_rate', 10, 4)->nullable()->after('exchange_rate_overridden_at')
                  ->comment('System rate before override, for audit trail');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'rate_source',
                'exchange_rate_note',
                'exchange_rate_overridden_by',
                'exchange_rate_overridden_at',
                'original_exchange_rate',
            ]);
        });
    }
};
