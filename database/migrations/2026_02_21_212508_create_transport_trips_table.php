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
        Schema::create('transport_trips', function (Blueprint $table) {
            $table->id();
                $table->foreignId('vehicle_id')
          ->constrained()
          ->onDelete('cascade');
    $table->foreignId('driver_id')
          ->constrained('users')
          ->onDelete('cascade');
    $table->dateTime('departure_time');
    $table->dateTime('estimated_arrival_time')->nullable();
    $table->dateTime('actual_arrival_time')->nullable();
    $table->enum('status', ['scheduled', 'active', 'completed', 'cancelled'])
          ->default('scheduled');
    $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_trips');
    }
};
