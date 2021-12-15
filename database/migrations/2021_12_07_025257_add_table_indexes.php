<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTableIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('monitored_job_meta', function (Blueprint $table) {
            $table->index(['type', 'monitored_job_id']);
        });
        Schema::table('monitored_jobs', function (Blueprint $table) {
            $table->index(['name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('monitored_jobs', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });
        Schema::table('monitored_job_meta', function (Blueprint $table) {
            $table->dropIndex(['type', 'monitored_job_id']);
        });
    }
}
