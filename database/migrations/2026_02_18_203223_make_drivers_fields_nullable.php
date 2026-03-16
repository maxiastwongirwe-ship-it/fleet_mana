<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
 public function up(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->string('license_number', 100)->nullable()->change();
            $table->string('license_category', 50)->nullable()->change();
            $table->date('license_issue_date')->nullable()->change();
            $table->date('license_expiry_date')->nullable()->change();
            $table->binary('nin_number')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->string('license_number', 100)->change();
            $table->string('license_category', 50)->nullable(false)->change();
            $table->date('license_issue_date')->nullable(false)->change();
            $table->date('license_expiry_date')->nullable(false)->change();
            $table->binary('nin_number')->nullable(false)->change();
        });
    }
};
