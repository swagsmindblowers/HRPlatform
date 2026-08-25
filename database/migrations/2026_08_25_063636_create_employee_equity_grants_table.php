<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeEquityGrantsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::enableForeignKeyConstraints();

        Schema::create('employee_equity_grants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('grant_type'); // options | rsu
            $table->unsignedInteger('units');
            $table->unsignedInteger('strike_price')->nullable(); // cents, options only
            $table->date('grant_date');
            $table->date('vesting_start_date');
            $table->unsignedSmallInteger('cliff_months')->default(12);
            $table->unsignedSmallInteger('vesting_months')->default(48);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_equity_grants');
    }
}
