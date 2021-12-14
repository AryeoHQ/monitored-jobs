<?php

namespace Aryeo\MonitoredJobs\Tests;

use Aryeo\MonitoredJobs\Tests\Fixtures\Jobs\ExampleSuccessfulJob;
use Aryeo\MonitoredJobs\Tests\Fixtures\Models\User;

class HandleJobProcessedTest extends MonitoredJobTestCase
{
    public function testItTracksProcessedStatus()
    {
        // Given
        $user = User::factory()->create();

        // When
        ExampleSuccessfulJob::dispatch($user, 'bar');

        // Then
        $job = $this->getMonitoredJob(ExampleSuccessfulJob::class);
        $this->assertJobMetadata($job, 'status', 'processed');
        $this->assertJobMetadata($job, 'tag', User::class.':1');
        $this->assertJobMetadata($job, 'tag', 'foo:bar');
    }
}
