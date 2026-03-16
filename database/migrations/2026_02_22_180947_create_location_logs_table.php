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
        Schema::create('location_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->foreignId('driver_id')->constrained('users')->onDelete('cascade');
            $table->decimal('latitude', 10, 7);   // e.g. -1.2921
            $table->decimal('longitude', 10, 7);  // e.g. 36.8219
            $table->float('accuracy')->nullable();  // in meters
            $table->float('speed')->nullable();     // in m/s
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('location_logs');
    }
};
