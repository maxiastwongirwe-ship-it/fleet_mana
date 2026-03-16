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
        Schema::create('transport_request_passengers', function (Blueprint $table) {
            $table->id();
                $table->foreignId('transport_request_id')
          ->constrained()
          ->onDelete('cascade');
    $table->string('passenger_name', 255);
    $table->foreignId('user_id')->nullable()
          ->constrained('users')
          ->onDelete('set null');   // if employee leaves system
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_request_passengers');
    }
};
