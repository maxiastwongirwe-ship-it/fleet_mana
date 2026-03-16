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
        Schema::create('fuel_logs', function (Blueprint $table) {
            $table->id();

            // Link to the original approved request (optional but useful)
            $table->foreignId('fuel_request_id')
                  ->nullable()
                  ->constrained('fuel_requests')
                  ->nullOnDelete();

            // Vehicle & Driver
            $table->foreignId('vehicle_id')
                  ->constrained('vehicles')
                  ->cascadeOnDelete();

            $table->foreignId('driver_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // Fuel & Odometer
            $table->decimal('litres_dispensed', 10, 2);          // actual fuel added
            $table->unsignedInteger('odometer_reading');         // must be > previous for this vehicle
            $table->string('fuel_type', 50)->nullable();         // petrol/diesel/...
            $table->string('station_name', 150)->nullable();     // where it was filled
            $table->dateTime('filled_at')->useCurrent();

            // Cost & Payment
            $table->decimal('total_cost', 12, 2)->nullable();
            $table->string('payment_method', 50)->nullable();    // cash, card, voucher, etc.

            // Admin / Verification
            $table->foreignId('logged_by')
                  ->constrained('users')
                  ->cascadeOnDelete();  // who recorded the fill-up

            $table->text('notes')->nullable();

            // Photo proof (odometer + receipt if needed)
            $table->string('odometer_photo_path', 2048)->nullable();
            $table->string('receipt_photo_path', 2048)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuel_logs');
    }
};
