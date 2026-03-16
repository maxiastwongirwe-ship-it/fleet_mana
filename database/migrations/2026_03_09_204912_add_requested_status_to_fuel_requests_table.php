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
            // Change enum to include 'requested'
            $table->enum('status', [
                'requested', 'pending', 'approved', 'rejected', 'completed'
            ])->default('requested')->change();
        });
    }

    public function down(): void
    {
        Schema::table('fuel_requests', function (Blueprint $table) {
            $table->enum('status', [
                'pending', 'approved', 'rejected', 'completed'
            ])->default('pending')->change();
        });
    }
};
