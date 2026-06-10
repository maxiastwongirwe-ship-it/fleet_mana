<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fuel_requests', function (Blueprint $table) {

            $table->string('theft_prediction')->nullable();
            $table->text('theft_prediction_message')->nullable();

            $table->decimal('expected_litres', 10, 2)->nullable();
            $table->decimal('fuel_difference', 10, 2)->nullable();

        });
    }

    public function down(): void
    {
        Schema::table('fuel_requests', function (Blueprint $table) {

            $table->dropColumn([
                'theft_prediction',
                'theft_prediction_message',
                'expected_litres',
                'fuel_difference'
            ]);
        });
    }
};
