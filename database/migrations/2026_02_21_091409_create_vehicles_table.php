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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('plate_number', 50)->unique(); // e.g. ABC123GP
            $table->string('make', 100)->nullable(); // e.g. Toyota
            $table->string('model', 100)->nullable(); // e.g. Hilux
            $table->year('year')->nullable();
            $table->enum('type', ['cargo', 'passenger'])->default('passenger'); // cargo or passenger
            $table->integer('capacity')->nullable(); // seats for passenger, load kg for cargo
            $table->string('fuel_type', 50)->nullable(); // petrol, diesel, electric
            $table->decimal('fuel_tank_capacity', 8, 2)->nullable(); // liters
            $table->unsignedInteger('current_odometer')->default(0); // km
            $table->string('vehicle_photo_path', 2048)->nullable(); // image
            $table->foreignId('assigned_driver_id')->nullable()->constrained('users')->nullOnDelete(); // link to driver
            $table->enum('status', ['active', 'maintenance', 'breakdown', 'retired'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
