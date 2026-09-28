<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSyncFieldsToExpensesAndTimesheets extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->string('external_reference')->nullable()->after('id');
            $table->string('sync_status')->nullable()->after('external_reference'); // null | pending | synced | failed
            $table->timestamp('synced_at')->nullable()->after('sync_status');
        });

        Schema::table('timesheets', function (Blueprint $table) {
            $table->string('external_reference')->nullable()->after('id');
            $table->string('sync_status')->nullable()->after('external_reference');
            $table->timestamp('synced_at')->nullable()->after('sync_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn(['external_reference', 'sync_status', 'synced_at']);
        });

        Schema::table('timesheets', function (Blueprint $table) {
            $table->dropColumn(['external_reference', 'sync_status', 'synced_at']);
        });
    }
}
