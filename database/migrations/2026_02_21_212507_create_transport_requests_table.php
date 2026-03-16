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
        Schema::create('transport_requests', function (Blueprint $table) {
            $table->id();
              $table->enum('request_type', ['passenger', 'goods'])->index();
    $table->foreignId('requested_by')
          ->constrained('users')
          ->onDelete('cascade');
    $table->string('pickup_location', 255);
    $table->string('dropoff_location', 255);
    $table->dateTime('pickup_time');
    $table->text('purpose')->nullable();
    $table->enum('status', ['pending', 'approved', 'rejected', 'grouped', 'assigned', 'completed'])
          ->default('pending');
    $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_requests');
    }
};
