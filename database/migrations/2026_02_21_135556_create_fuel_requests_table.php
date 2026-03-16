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
         Schema::create('fuel_requests', function (Blueprint $table) {
            $table->id();

            // Who requested (driver / worker)
            $table->foreignId('requested_by')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // Vehicle to be refueled
            $table->foreignId('vehicle_id')
                  ->constrained('vehicles')
                  ->cascadeOnDelete();

            // Fuel details
            $table->decimal('requested_amount', 10, 2); // liters
            $table->string('fuel_type', 50)->nullable(); // petrol, diesel, etc.
            $table->text('reason')->nullable(); // e.g. "Long trip to Arua"
            $table->dateTime('requested_at')->useCurrent();

            // Status & approval
            $table->enum('status', [
                'pending',      // waiting for approval
                'approved',     // cleared to refuel
                'rejected',     // not approved
                'completed',    // fuel actually given
            ])->default('pending');

            $table->foreignId('approved_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamp('approved_at')->nullable();

            // Admin notes / rejection reason
            $table->text('admin_notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuel_requests');
    }
};
