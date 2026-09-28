<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePerformanceReviewsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::enableForeignKeyConstraints();

        Schema::create('performance_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('reviewer_id');
            $table->string('review_type'); // 30_day | 60_day | 90_day | ad_hoc
            $table->date('review_date');
            $table->unsignedTinyInteger('rating')->nullable();
            $table->text('strengths')->nullable();
            $table->text('areas_for_improvement')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('draft'); // draft | submitted
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('reviewer_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_reviews');
    }
}
