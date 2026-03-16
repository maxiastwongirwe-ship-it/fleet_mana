<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('breakdowns', function (Blueprint $table) {
            $table->id();

            // The vehicle that broke down
            $table->foreignId('vehicle_id')
                  ->constrained('vehicles')
                  ->cascadeOnDelete();

            // The driver who reported it (usually the one driving at the time)
            $table->foreignId('driver_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // Who approved the breakdown report (admin/supervisor)
            $table->foreignId('approved_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // Core details
            $table->string('location', 255)->nullable();           // Where it happened
            $table->text('description');                           // What happened
            $table->dateTime('occurred_at');                       // When it happened
            $table->enum('severity', ['minor', 'moderate', 'major', 'critical'])->default('moderate');
            $table->enum('status', [
                'reported',         // Just reported
                'acknowledged',     // Admin saw it
                'in_progress',      // Being handled
                'resolved',         // Fixed
                'rejected'          // Not accepted as valid
            ])->default('reported');

            // Cost & notes
            $table->decimal('estimated_cost', 12, 2)->nullable();
            $table->decimal('actual_cost', 12, 2)->nullable();
            $table->text('admin_notes')->nullable();

            // Photos (multiple possible — we’ll store paths as JSON array or separate table later)
            $table->json('photo_paths')->nullable();  // e.g. ["path1.jpg", "path2.jpg"]

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('breakdowns');
    }
};
