<?php

namespace Aryeo\MonitoredJobs\Tests;

use Aryeo\MonitoredJobs\Models\MonitoredJob;
use Aryeo\MonitoredJobs\Models\MonitoredJobMeta;

class MonitoredJobTestCase extends TestCase
{
    protected function getMonitoredJob(string $jobClass): MonitoredJob
    {
        $this->assertJob($jobClass);

        return MonitoredJob::where('name', $jobClass)->first();
    }

    protected function assertJob(string $jobClass): void
    {
        $this->assertDatabaseHas('monitored_jobs', [
            'name' => $jobClass,
        ]);
    }

    protected function assertJobMetadata(MonitoredJob $job, string $type, $value): void
    {
        $this->assertDatabaseHas('monitored_job_meta', [
            'monitored_job_id' => $job->id,
            'type' => $type,
            'value' => $value,
        ]);
    }

    protected function assertJobMetadataOrdered(MonitoredJob $job, array $expectedMetadata): void
    {
        $meta = $job->meta->sortBy('created_at')->sortBy('id')->map(function (MonitoredJobMeta $meta) {
            return [$meta->type => $meta->value];
        })->toArray();

        $this->assertEquals($expectedMetadata, $meta);
    }

    protected function assertJobException(MonitoredJob $job, $exception): void
    {
        $this->assertDatabaseHas('monitored_job_exceptions', [
            'monitored_job_id' => $job->id,
            'exception' => $exception,
        ]);
    }
}
