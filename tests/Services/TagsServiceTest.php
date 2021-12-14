<?php

namespace Aryeo\MonitoredJobs\Tests\Services;

use Aryeo\MonitoredJobs\Services\TagsService;
use Aryeo\MonitoredJobs\Tests\Fixtures\Jobs\ExampleSuccessfulJob;
use Aryeo\MonitoredJobs\Tests\Fixtures\Models\User;
use Aryeo\MonitoredJobs\Tests\MonitoredJobTestCase;

class TagsServiceTest extends MonitoredJobTestCase
{
    /** @var User */
    private $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function testItExtractsModelsFromJob()
    {
        // Given
        $job = new ExampleSuccessfulJob($this->user, $this->user);

        // When
        $tags = (resolve(TagsService::class))->for($job);

        // Then
        $this->assertContains(User::class.':1', $tags);
    }

    public function testItExtractsStringsFromJob()
    {
        // Given
        $job = new ExampleSuccessfulJob($this->user, 'bar');

        // When
        $tags = (resolve(TagsService::class))->for($job);

        // Then
        $this->assertContains('foo:bar', $tags);
    }

    public function testItExtractsNumbersFromJob()
    {
        // Given
        $job = new ExampleSuccessfulJob($this->user, 100);

        // When
        $tags = (resolve(TagsService::class))->for($job);

        // Then
        $this->assertContains('foo:100', $tags);
    }

    public function testItExtractsArraysFromJob()
    {
        // Given
        $job = new ExampleSuccessfulJob($this->user, [1, 2, 3]);

        // When
        $tags = (resolve(TagsService::class))->for($job);

        // Then
        $this->assertContains('foo:[1,2,3]', $tags);
    }

    public function testItDoesNotExtractObjectsFromJob()
    {
        // Given
        $object = new \stdClass(['hello' => 'world']);

        $job = new ExampleSuccessfulJob($this->user, $object);

        // When
        $tags = (resolve(TagsService::class))->for($job);

        // Then
        $this->assertCount(1, $tags);
    }
}
