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
         Schema::table('fuel_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('fuel_requests', 'price_per_litre')) {
                $table->decimal('price_per_litre', 10, 2)->nullable()->after('actual_litres_dispensed');
            }

            if (!Schema::hasColumn('fuel_requests', 'promocode')) {
                $table->string('promocode', 100)->nullable()->after('payment_method');
            }

            if (!Schema::hasColumn('fuel_requests', 'bank_account')) {
                $table->string('bank_account', 100)->nullable()->after('promocode');
            }

            if (!Schema::hasColumn('fuel_requests', 'card_details')) {
                $table->string('card_details', 100)->nullable()->after('bank_account');
            }

            // Ensure these core completion fields exist
            if (!Schema::hasColumn('fuel_requests', 'receipt_photo_path')) {
                $table->string('receipt_photo_path', 2048)->nullable()->after('odometer_photo_path');
            }
            if (!Schema::hasColumn('fuel_requests', 'station_name')) {
                $table->string('station_name', 150)->nullable()->after('receipt_photo_path');
            }
            if (!Schema::hasColumn('fuel_requests', 'total_cost')) {
                $table->decimal('total_cost', 12, 2)->nullable()->after('station_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('fuel_requests', function (Blueprint $table) {
            $table->dropColumn([
                'price_per_litre',
                'promocode',
                'bank_account',
                'card_details',
                // only drop if you are sure — usually better to comment these out in down()
                // 'receipt_photo_path',
                // 'station_name',
                // 'total_cost',
            ]);
        });
    }
};
