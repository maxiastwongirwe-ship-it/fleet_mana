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
       Schema::create('vehicle_documents', function (Blueprint $table) {
            $table->id();

            // Link to vehicle
            $table->foreignId('vehicle_id')
                  ->constrained('vehicles')
                  ->cascadeOnDelete();

            // Document type
            $table->enum('document_type', [
                'insurance',
                'third_party',
                'inspection',
                'permit',
                'roadworthy',
                'license',
                'other'
            ]);

            // Details
            $table->string('document_number', 100)->nullable(); // policy/policy number
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();

            // File storage
            $table->string('file_path', 2048)->nullable(); // stored path (PDF/image)

            // Who uploaded & when
            $table->foreignId('uploaded_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // Status & notes
            $table->boolean('is_valid')->default(true);
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_documents');
    }
};
