<?php

namespace Aryeo\MonitoredJobs\Tests\Models;

use Aryeo\MonitoredJobs\Models\MonitoredJob;
use Aryeo\MonitoredJobs\Tests\MonitoredJobTestCase;

class MonitoredJobTest extends MonitoredJobTestCase
{
    public function testItStoresMicrosecondsForCreatedAt()
    {
        // Given
        $monitoredJob = MonitoredJob::factory()->create();

        // Then
        $this->assertEquals(now()->format('Y-m-d H:i:s.u'), $monitoredJob->created_at->format('Y-m-d H:i:s.u'));
        $this->assertEquals(now()->micro, $monitoredJob->created_at->micro);
    }

    public function testItCanAddMetadata()
    {
        // Given
        $monitoredJob = MonitoredJob::factory()->create();

        // When
        $monitoredJob->addMeta('status', 'processing');

        // Then
        $this->assertDatabaseHas('monitored_job_meta', [
            'monitored_job_id' => $monitoredJob->id,
            'type' => 'status',
            'value' => 'processing',
        ]);
    }

    public function testItReturnsLatestMetadataForGivenType()
    {
        // Given
        $monitoredJob = MonitoredJob::factory()->create();

        $monitoredJob->addMeta('status', 'processing');
        $monitoredJob->addMeta('status', 'processed');

        // When
        $meta = $monitoredJob->latestMeta('status');

        // Then
        $this->assertEquals('processed', $meta->value);
    }

    public function testItReturnsLatestMetadataForGivenTypeAndValue()
    {
        // Given
        $monitoredJob = MonitoredJob::factory()->create();

        $first = $monitoredJob->addMeta('status', 'processing');
        $last = $monitoredJob->addMeta('status', 'processing');
        $outsideScope = $monitoredJob->addMeta('status', 'processed');

        // When
        $meta = $monitoredJob->latestMeta('status', 'processing');

        // Then
        $this->assertEquals($last->id, $meta->id);
    }

    public function testItReturnsDurationForSuccessfulJob()
    {
        // Given
        $monitoredJob = MonitoredJob::factory()->create();

        $monitoredJob->meta()->create([
            'type' => 'status',
            'value' => 'processing',
            'created_at' => now(),
        ]);
        $monitoredJob->meta()->create([
            'type' => 'status',
            'value' => 'processed',
            'created_at' => now()->addMinute(),
        ]);

        // When
        $duration = $monitoredJob->getDuration();

        // Then
        $this->assertEquals('1 minute', $duration);
    }

    public function testItReturnsDurationForFailedJob()
    {
        // Given
        $monitoredJob = MonitoredJob::factory()->create();

        $monitoredJob->meta()->create([
            'type' => 'status',
            'value' => 'processing',
            'created_at' => now(),
        ]);
        $monitoredJob->meta()->create([
            'type' => 'status',
            'value' => 'failed',
            'created_at' => now()->addMinute(),
        ]);

        // When
        $duration = $monitoredJob->getDuration();

        // Then
        $this->assertEquals('1 minute', $duration);
    }
}
