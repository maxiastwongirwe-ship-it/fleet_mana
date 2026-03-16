<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
      public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->unique()
                  ->constrained('users')
                  ->cascadeOnDelete();

            // License & compliance
            $table->string('license_number', 100);
            $table->string('license_category', 50)->nullable();
            $table->date('license_issue_date')->nullable();
            $table->date('license_expiry_date')->nullable();

            // Encrypted sensitive data
            $table->binary('nin_number')->nullable();

            // Driver photo (separate from user profile photo)
            $table->string('driver_photo_path', 2048)->nullable();

            // Status
            $table->enum('status', ['active', 'suspended', 'expired'])->default('active');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
