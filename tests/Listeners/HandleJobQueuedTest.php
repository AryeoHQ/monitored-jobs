<?php

namespace Aryeo\MonitoredJobs\Tests;

use Illuminate\Support\Facades\Config;
use Aryeo\MonitoredJobs\Tests\Fixtures\Jobs\ExampleSuccessfulJob;
use Aryeo\MonitoredJobs\Tests\Fixtures\Models\User;

class HandleJobQueuedTest extends MonitoredJobTestCase
{
    public function testItTracksQueuedStatus()
    {
        // Given
        $user = User::factory()->create();

        // Use the `redis` queue so that the job is actually queued up.
        // The `sync` queue does not fire the JobQueued event.
        Config::set('queue.default', 'redis');

        // When
        ExampleSuccessfulJob::dispatch($user, 'bar');

        // Then
        $job = $this->getMonitoredJob(ExampleSuccessfulJob::class);
        $this->assertNotNull($job->backtrace);
        $this->assertNull($job->payload);
        $this->assertJobMetadata($job, 'status', 'queued');
        $this->assertJobMetadata($job, 'tag', User::class.':1');
        $this->assertJobMetadata($job, 'tag', 'foo:bar');
    }
}
