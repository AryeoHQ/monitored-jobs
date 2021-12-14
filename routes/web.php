<?php

use Aryeo\MonitoredJobs\Http\Controllers\IndexMonitoredJobsController;
use Illuminate\Support\Facades\Route;

Route::get('/monitored-jobs', IndexMonitoredJobsController::class);
