<?php

namespace Aryeo\MonitoredJobs\Tests;

use Aryeo\MonitoredJobs\Tests\Fixtures\Jobs\ExampleFailedJob;
use Aryeo\MonitoredJobs\Tests\Fixtures\Models\User;

class HandleJobExceptionOccurredTest extends MonitoredJobTestCase
{
    public function testItTracksExceptionOccurredEvent()
    {
        // Given
        $user = User::factory()->create();

        // When
        $caughtException = null;
        try {
            ExampleFailedJob::dispatch($user);
        } catch (\Throwable $e) {
            $caughtException = $e;
        }

        // Then
        $job = $this->getMonitoredJob(ExampleFailedJob::class);
        $this->assertJobMetadata($job, 'status', 'failed');
        $this->assertJobMetadata($job, 'tag', User::class.':1');

        $this->assertNotNull($caughtException);
        $this->assertJobException($job, $caughtException);
    }
}
