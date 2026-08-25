<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOnboardingTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::enableForeignKeyConstraints();

        Schema::create('onboarding_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable(); // null = system default template
            $table->string('name');
            $table->string('jurisdiction'); // generic | uk | us
            $table->boolean('is_contractor_template')->default(false);
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });

        Schema::create('onboarding_template_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('onboarding_template_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type'); // task | compliance_deadline
            $table->integer('offset_days_from_start')->default(0);
            $table->boolean('is_legally_mandated')->default(false);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->foreign('onboarding_template_id', 'fk_onboarding_template_items_template')
                ->references('id')->on('onboarding_templates')->onDelete('cascade');
        });

        Schema::create('employee_onboarding_checklists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('onboarding_template_id')->nullable();
            $table->date('started_at');
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('onboarding_template_id', 'fk_employee_onboarding_checklists_template')
                ->references('id')->on('onboarding_templates')->onDelete('set null');
        });

        Schema::create('employee_onboarding_checklist_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_onboarding_checklist_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type'); // task | compliance_deadline
            $table->boolean('is_legally_mandated')->default(false);
            $table->date('due_date')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->unsignedBigInteger('completed_by')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->foreign('employee_onboarding_checklist_id', 'fk_employee_onboarding_checklist_items_checklist')
                ->references('id')->on('employee_onboarding_checklists')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_onboarding_checklist_items');
        Schema::dropIfExists('employee_onboarding_checklists');
        Schema::dropIfExists('onboarding_template_items');
        Schema::dropIfExists('onboarding_templates');
    }
}
