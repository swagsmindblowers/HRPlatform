<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->boolean('betteroff_has_sponsor_licence')->default(false)->after('code_to_join_company');
            $table->string('betteroff_employer_size_class')->default('small')->after('betteroff_has_sponsor_licence'); // small | large
            $table->unsignedTinyInteger('betteroff_default_employer_visa_share_percent')->default(100)->after('betteroff_employer_size_class');
        });
    }

    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'betteroff_has_sponsor_licence',
                'betteroff_employer_size_class',
                'betteroff_default_employer_visa_share_percent',
            ]);
        });
    }
};
