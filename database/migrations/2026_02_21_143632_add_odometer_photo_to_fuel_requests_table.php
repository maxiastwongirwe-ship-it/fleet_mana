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
            // Odometer reading when the fuel was requested / filled
            $table->unsignedInteger('odometer_reading')->nullable()->after('requested_amount');

            // Photo of the odometer (proof)
            $table->string('odometer_photo_path', 2048)->nullable()->after('odometer_reading');

            // Actual liters dispensed (filled in after approval/completion)
            $table->decimal('actual_litres_dispensed', 10, 2)->nullable()->after('odometer_photo_path');
        });
    }

    public function down(): void
    {
        Schema::table('fuel_requests', function (Blueprint $table) {
            $table->dropColumn([
                'odometer_reading',
                'odometer_photo_path',
                'actual_litres_dispensed'
            ]);
        });
    }
};
