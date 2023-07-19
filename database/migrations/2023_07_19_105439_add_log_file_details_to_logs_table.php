<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLogFileDetailsToLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->string('log_file_name')->after('id');
            $table->timestamp('file_last_updated_at')->after('log_file_name');
            $table->unsignedBigInteger('line_number')->after('file_last_updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->dropColumn(['log_file_name', 'file_last_updated_at', 'line_number']);
        });
    }
}
