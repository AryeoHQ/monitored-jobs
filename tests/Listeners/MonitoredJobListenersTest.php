<?php

namespace Aryeo\MonitoredJobs\Tests;

use Aryeo\MonitoredJobs\Tests\Fixtures\Jobs\ExampleFailedJob;
use Aryeo\MonitoredJobs\Tests\Fixtures\Jobs\ExampleJobWithNoParameters;
use Aryeo\MonitoredJobs\Tests\Fixtures\Jobs\ExampleSuccessfulJob;
use Aryeo\MonitoredJobs\Tests\Fixtures\Models\User;
use Illuminate\Support\Facades\Config;

class MonitoredJobListenersTest extends MonitoredJobTestCase
{
    public function testItDoesNotMonitorJobsWhenDisabled()
    {
        // Given
        Config::set('monitored-jobs.enabled', false);

        $user = User::factory()->create();

        // When
        ExampleSuccessfulJob::dispatch($user, 'bar');

        // Then
        $this->assertDatabaseMissing('monitored_jobs', [
            'name' => ExampleSuccessfulJob::class,
        ]);
    }

    public function testMonitorsAllJobsByDefault()
    {
        // Given
        Config::set('monitored-jobs.enabled', true);

        $user = User::factory()->create();

        // When
        ExampleSuccessfulJob::dispatch($user, 'bar');

        // Then
        $this->assertDatabaseHas('monitored_jobs', [
            'name' => ExampleSuccessfulJob::class,
        ]);
    }

    public function testItDoesNotMonitorAnyJobWhenIncludeConfigIsEmpty()
    {
        // Given
        Config::set('monitored-jobs.enabled', true);
        Config::set('monitored-jobs.include_jobs', []);

        $user = User::factory()->create();

        // When
        ExampleSuccessfulJob::dispatch($user, 'bar');

        // Then
        $this->assertDatabaseMissing('monitored_jobs', [
            'name' => ExampleSuccessfulJob::class,
        ]);
    }

    public function testItDoesNotMonitorJobWhenNotInIncludeConfig()
    {
        // Given
        Config::set('monitored-jobs.enabled', true);
        Config::set('monitored-jobs.include_jobs', [ExampleFailedJob::class]);

        $user = User::factory()->create();

        // When
        ExampleSuccessfulJob::dispatch($user, 'bar');

        // Then
        $this->assertDatabaseMissing('monitored_jobs', [
            'name' => ExampleSuccessfulJob::class,
        ]);
    }

    public function testItDoesNotMonitorJobWhenInExcludeConfig()
    {
        // Given
        Config::set('monitored-jobs.enabled', true);
        Config::set('monitored-jobs.exclude_jobs', [ExampleSuccessfulJob::class]);

        $user = User::factory()->create();

        // When
        ExampleSuccessfulJob::dispatch($user, 'bar');

        // Then
        $this->assertDatabaseMissing('monitored_jobs', [
            'name' => ExampleSuccessfulJob::class,
        ]);
    }

    public function testItDoesNotMonitorJobWhenInIncludeAndExcludeConfig()
    {
        // Given
        Config::set('monitored-jobs.enabled', true);
        Config::set('monitored-jobs.include_jobs', [ExampleSuccessfulJob::class]);
        Config::set('monitored-jobs.exclude_jobs', [ExampleSuccessfulJob::class]);

        $user = User::factory()->create();

        // When
        ExampleSuccessfulJob::dispatch($user, 'bar');

        // Then
        $this->assertDatabaseMissing('monitored_jobs', [
            'name' => ExampleSuccessfulJob::class,
        ]);
    }

    public function testItRecordsMonitoredJobForSuccessfulAttempt()
    {
        // Given
        $user = User::factory()->create();

        // When
        ExampleSuccessfulJob::dispatch($user, 'bar');

        // Then
        $job = $this->getMonitoredJob(ExampleSuccessfulJob::class);

        $this->assertJobMetadataOrdered($job, [
            ['tag' => User::class.':1'],
            ['tag' => 'foo:bar'],
            ['status' => 'processing'],
            ['status' => 'processed'],
        ]);
    }

    public function testItRecordsMonitoredJobForJobWithoutParameters()
    {
        // When
        ExampleJobWithNoParameters::dispatch();

        // Then
        $job = $this->getMonitoredJob(ExampleJobWithNoParameters::class);

        $this->assertJobMetadataOrdered($job, [
            ['status' => 'processing'],
            ['status' => 'processed'],
        ]);
    }

    public function testItRecordsMonitoredJobForFailedAttempt()
    {
        // Given
        $user = User::factory()->create();

        // When
        $caughtException = null;
        try {
            ExampleFailedJob::dispatch($user);
        } catch (\Throwable $e) {
            // Have to catch the exception from the job here because it runs synchronously
            $caughtException = $e;
        }

        // Then
        $this->assertNotNull($caughtException);

        $job = $this->getMonitoredJob(ExampleFailedJob::class);

        $this->assertCount(4, $job->meta);
        $this->assertJobMetadataOrdered($job, [
            ['tag' => User::class.':1'],
            ['status' => 'processing'],
            ['event' => 'exception_occurred'],
            ['status' => 'failed'],
        ]);

        $this->assertCount(1, $job->exceptions);
        $this->assertJobException($job, $caughtException);
    }
}
