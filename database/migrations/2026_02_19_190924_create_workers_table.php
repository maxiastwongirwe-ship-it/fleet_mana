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
       Schema::create('workers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->unique()
                  ->constrained()
                  ->onDelete('cascade');

            // Worker-specific identifiers & data
            $table->string('work_id', 32)->unique()->nullable()->comment('Internal company work/employee code');
            $table->string('nin', 20)->unique()->nullable()->comment('National Identification Number');
            $table->string('department', 80)->nullable();
            $table->string('position', 100)->nullable();           // e.g. Loader, Mechanic, Cleaner
            $table->date('hire_date')->nullable();
            $table->date('contract_end_date')->nullable();

            // Payroll & compliance related (common in many countries)
            $table->string('tin', 20)->unique()->nullable()->comment('Tax Identification Number');
            $table->string('nssf_number', 30)->nullable()->comment('Social security number');
            $table->string('bank_name', 100)->nullable();
            $table->string('bank_account_number', 40)->nullable();

            $table->enum('employment_type', ['permanent', 'contract', 'casual', 'probation'])
                  ->default('contract');

            $table->boolean('has_uniform')->default(false);
            $table->boolean('has_id_card')->default(false);
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workers');
    }
};
