<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('betteroff_rate_versions', function (Blueprint $table) {
            $table->id();
            $table->string('key')->index();
            $table->decimal('value', 14, 4);
            $table->string('unit')->default('gbp');
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->string('source_url');
            $table->string('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['key', 'effective_from', 'effective_to']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('betteroff_rate_versions');
    }
};
