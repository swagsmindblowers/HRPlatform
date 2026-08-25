<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComplianceAndPolicyTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::enableForeignKeyConstraints();

        Schema::create('company_compliance_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('title');
            $table->string('category'); // insurance | tax | pension | immigration
            $table->string('jurisdiction');
            $table->string('status')->default('not_started'); // not_started | in_progress | complete
            $table->date('due_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });

        Schema::create('policy_templates', function (Blueprint $table) {
            $table->id();
            $table->string('jurisdiction');
            $table->string('category');
            $table->string('title');
            $table->longText('body');
            $table->boolean('is_system_default')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('policy_templates');
        Schema::dropIfExists('company_compliance_items');
    }
}
