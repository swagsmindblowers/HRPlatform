<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('betteroff_scenarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('job_opening_id')->nullable()->constrained('job_openings')->nullOnDelete();
            $table->foreignId('candidate_id')->nullable()->constrained('candidates')->nullOnDelete();
            $table->foreignId('created_by_user_id')->nullable();
            $table->string('hire_type'); // uk | sponsored
            $table->date('as_of_date');
            $table->string('rates_version_tag');
            $table->json('inputs');
            $table->json('employer_result');
            $table->json('candidate_result')->nullable();
            $table->string('share_token')->nullable()->unique();
            $table->timestamp('share_expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('betteroff_scenario_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scenario_id')->constrained('betteroff_scenarios')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable();
            $table->string('action'); // ran | saved | shared
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('betteroff_scenario_events');
        Schema::dropIfExists('betteroff_scenarios');
    }
};
