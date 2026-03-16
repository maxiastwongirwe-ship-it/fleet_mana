<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('breakdowns', function (Blueprint $table) {
            // If you don't have approved_by yet (from earlier code it exists, but just in case)
            if (!Schema::hasColumn('breakdowns', 'approved_by')) {
                $table->foreignId('approved_by')
                      ->nullable()
                      ->constrained('users')
                      ->nullOnDelete()
                      ->after('driver_id');
            }

            // New boolean field for approval status
            $table->boolean('approved')->default(false)->after('photo_paths');
        });
    }

    public function down(): void
    {
        Schema::table('breakdowns', function (Blueprint $table) {
            $table->dropColumn('approved');
        });
    }
};
