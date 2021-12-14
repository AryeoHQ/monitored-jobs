<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonitoredJobTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monitored_jobs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('name');
            $table->string('connection');
            $table->string('queue')->nullable();
            $table->longText('payload')->nullable();
            $table->integer('max_tries')->nullable();
            $table->integer('max_exceptions')->nullable();
            $table->integer('timeout')->nullable();
            $table->dateTime('retry_until')->nullable();
            $table->timestamps(3);
        });

        Schema::create('monitored_job_meta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monitored_job_id')->constrained();
            $table->string('type');
            $table->text('value');
            $table->timestamps(5);
        });

        Schema::create('monitored_job_exceptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monitored_job_id')->constrained();
            $table->longText('exception');
            $table->timestamps(5);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('monitored_job_exceptions');
        Schema::dropIfExists('monitored_job_meta');
        Schema::dropIfExists('monitored_jobs');
    }
}
