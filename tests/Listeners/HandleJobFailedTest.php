<?php

namespace Aryeo\MonitoredJobs\Tests;

use Aryeo\MonitoredJobs\Tests\Fixtures\Jobs\ExampleFailedJob;
use Aryeo\MonitoredJobs\Tests\Fixtures\Models\User;

class HandleJobFailedTest extends MonitoredJobTestCase
{
    public function testItTracksFailedStatus()
    {
        // Given
        $user = User::factory()->create();

        $this->expectException(\Exception::class);

        // When
        ExampleFailedJob::dispatch($user);

        // Then
        $job = $this->getMonitoredJob(ExampleFailedJob::class);
        $this->assertJobMetadata($job, 'status', 'failed');
        $this->assertJobMetadata($job, 'tag', User::class.':1');
    }
}
