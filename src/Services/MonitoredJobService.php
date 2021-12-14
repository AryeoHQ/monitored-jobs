<?php

namespace Aryeo\MonitoredJobs\Services;

use Illuminate\Contracts\Queue\Job;
use Illuminate\Queue\Events\JobQueued;
use Aryeo\MonitoredJobs\Models\MonitoredJob;
use Aryeo\MonitoredJobs\Models\MonitoredJobMeta;

class MonitoredJobService
{
    public function __construct(public TagsService $tags)
    {
    }

    public function createQueuedJob(JobQueued $event): MonitoredJob
    {
        /* @var MonitoredJob */
        $monitoredJob = MonitoredJob::create([
            'uuid' => $event->id,
            'name' => get_class($event->job),
            'connection' => $event->connectionName,
            'queue' => null,
            'payload' => null,
            'max_tries' => null,
            'max_exceptions' => null,
            'timeout' => null,
            'retry_until' => null,
            'backtrace' => $this->getBacktrace(),
        ]);

        $monitoredJob->meta()->createMany(
            $this->tags->for($event->job)->map(function ($tag) {
                return [
                    'type' => MonitoredJobMeta::TYPE_TAG,
                    'value' => $tag,
                ];
            })->toArray()
        );
        $monitoredJob->addMeta(MonitoredJobMeta::TYPE_STATUS, 'queued');

        return $monitoredJob;
    }

    public function getOrCreateMonitoredJob(Job $job): MonitoredJob
    {
        $jobDetails = $this->getJobDetails($job);

        $monitoredJob = MonitoredJob::firstOrCreate([
            'uuid' => $job->uuid(),
        ], $jobDetails);

        if ($monitoredJob->wasRecentlyCreated) {
            $monitoredJob->meta()->createMany($this->getJobTags($job));
        }

        if (!$monitoredJob->queue) {
            // If the `queue` property is missing, it means this job's details haven't been populated yet
            $monitoredJob->update($jobDetails);
        }

        return $monitoredJob;
    }

    private function getJobDetails(Job $job): array
    {
        return [
            'name' => $job->resolveName(),
            'connection' => $job->getConnectionName(),
            'queue' => $job->getQueue(),
            'payload' => $job->payload(),
            'max_tries' => $job->maxTries(),
            'max_exceptions' => $job->maxExceptions(),
            'timeout' => $job->timeout(),
            'retry_until' => $job->retryUntil(),
        ];
    }

    private function getJobTags(Job $job): array
    {
        $dispatchedJob = unserialize($job->payload()['data']['command'] ?? null);
        if ($dispatchedJob) {
            return collect($this->tags->for($dispatchedJob))->map(function ($tag) {
                return [
                    'type' => MonitoredJobMeta::TYPE_TAG,
                    'value' => $tag,
                ];
            })->toArray();
        }

        return [];
    }

    public function getBacktrace(): ?string
    {
        $backtrace = collect(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 50))
            ->filter(function ($caller) {
                $file = $caller['file'] ?? null;

                return $file && !str_contains($file, 'vendor/laravel/framework');
            })
            ->map(fn ($caller) => "{$caller['file']}:{$caller['line']}");

        return $backtrace->isNotEmpty() ? $backtrace->join("\n") : null;
    }
}
