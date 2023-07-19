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
        Schema::table('logs', function (Blueprint $table) {
            // Create an index on the 'service_name' column
            $table->index('service_name');

            // Create an index on the 'status' column
            $table->index('status');

            // Create an index on the 'logged_at' column
            $table->index('logged_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('logs', function (Blueprint $table) {
            // Drop the indexes if needed
            $table->dropIndex('logs_service_name_index');
            $table->dropIndex('logs_status_index');
            $table->dropIndex('logs_logged_at_index');
        });
    }
};
