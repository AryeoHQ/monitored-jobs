<?php

namespace Aryeo\MonitoredJobs\Tests\Factories;

use Aryeo\MonitoredJobs\Models\MonitoredJob;
use Aryeo\MonitoredJobs\Tests\Fixtures\Jobs\ExampleSuccessfulJob;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MonitoredJobFactory extends Factory
{
    protected $model = MonitoredJob::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'uuid' => Str::uuid()->toString(),
            'name' => ExampleSuccessfulJob::class,
            'connection' => 'default',
            'queue' => 'default',
            'payload' => json_encode([]),
            'max_tries' => 5,
            'max_exceptions' => null,
            'timeout' => null,
            'retry_until' => null,
        ];
    }

    public function withException()
    {
        return $this->afterCreating(function (MonitoredJob $job) {
            $exception = null;
            try {
                throw new \Exception('Test Exception');
            } catch (\Throwable $e) {
                $exception = $e;
            }

            $job->addException($exception);
        });
    }
}
