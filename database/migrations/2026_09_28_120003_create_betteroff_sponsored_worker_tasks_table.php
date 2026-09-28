<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('betteroff_sponsored_workers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('scenario_id')->nullable()->constrained('betteroff_scenarios')->nullOnDelete();
            $table->foreignId('candidate_id')->nullable()->constrained('candidates')->nullOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->date('confirmed_start_date')->nullable();
            $table->timestamps();
        });

        Schema::create('betteroff_sponsored_worker_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sponsored_worker_id')->constrained('betteroff_sponsored_workers')->cascadeOnDelete();
            $table->foreignId('company_compliance_item_id')->nullable()->constrained('company_compliance_items')->nullOnDelete();
            $table->string('type'); // right_to_work_check | certificate_of_sponsorship | reporting_deadline
            $table->date('due_date')->nullable();
            $table->string('status')->default('not_started');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('betteroff_sponsored_worker_tasks');
        Schema::dropIfExists('betteroff_sponsored_workers');
    }
};
