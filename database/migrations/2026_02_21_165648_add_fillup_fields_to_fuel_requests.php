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
        // Only add if the column doesn't exist
        if (!Schema::hasColumn('fuel_requests', 'actual_litres_dispensed')) {
            $table->decimal('actual_litres_dispensed', 10, 2)->nullable()->after('requested_amount');
        }

        if (!Schema::hasColumn('fuel_requests', 'odometer_reading')) {
            $table->unsignedInteger('odometer_reading')->nullable()->after('actual_litres_dispensed');
        }

        if (!Schema::hasColumn('fuel_requests', 'odometer_photo_path')) {
            $table->string('odometer_photo_path', 2048)->nullable()->after('odometer_reading');
        }

        if (!Schema::hasColumn('fuel_requests', 'receipt_photo_path')) {
            $table->string('receipt_photo_path', 2048)->nullable()->after('odometer_photo_path');
        }

        if (!Schema::hasColumn('fuel_requests', 'station_name')) {
            $table->string('station_name', 150)->nullable()->after('receipt_photo_path');
        }

        if (!Schema::hasColumn('fuel_requests', 'total_cost')) {
            $table->decimal('total_cost', 12, 2)->nullable()->after('station_name');
        }

        if (!Schema::hasColumn('fuel_requests', 'payment_method')) {
            $table->string('payment_method', 50)->nullable()->after('total_cost');
        }

        if (!Schema::hasColumn('fuel_requests', 'fillup_notes')) {
            $table->text('fillup_notes')->nullable()->after('payment_method');
        }
    });
}

    public function down(): void
    {
        Schema::table('fuel_requests', function (Blueprint $table) {
            $table->dropColumn([
                'actual_litres_dispensed',
                'odometer_at_fillup',
                'odometer_photo_path',
                'receipt_photo_path',
                'station_name',
                'total_cost',
                'payment_method',
                'fillup_notes',
            ]);
        });
    }
};
