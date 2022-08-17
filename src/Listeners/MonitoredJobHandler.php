<?php

namespace Aryeo\MonitoredJobs\Listeners;

use Aryeo\MonitoredJobs\Models\MonitoredJob;
use Aryeo\MonitoredJobs\Models\MonitoredJobMeta;
use Aryeo\MonitoredJobs\Services\MonitoredJobService;
use Illuminate\Contracts\Queue\Job;

class MonitoredJobHandler
{
    public function __construct(protected MonitoredJobService $jobService)
    {
    }

    protected function isEnabled($job): bool
    {
        if (!config('monitored-jobs.enabled', true)) {
            return false;
        }

        $jobClass = is_object($job) ? get_class($job) : $job;

        $excludeJobs = config('monitored-jobs.exclude_jobs', null);
        if (!is_null($excludeJobs) && in_array($jobClass, $excludeJobs)) {
            return false;
        }

        $includeJobs = config('monitored-jobs.include_jobs', null);
        if (is_null($includeJobs) || in_array($jobClass, $includeJobs)) {
            return true;
        }

        return false;
    }

    protected function trackStatus(Job $job, string $status): MonitoredJob
    {
        return $this->recordMeta($job, MonitoredJobMeta::TYPE_STATUS, $status);
    }

    protected function trackEvent(Job $job, string $event): MonitoredJob
    {
        return $this->recordMeta($job, MonitoredJobMeta::TYPE_EVENT, $event);
    }

    protected function recordMeta(Job $job, string $type, $value): MonitoredJob
    {
        $monitoredJob = $this->jobService->getOrCreateMonitoredJob($job);
        $monitoredJob->addMeta($type, $value);

        return $monitoredJob;
    }

    protected function recordException(Job $job, $exception): MonitoredJob
    {
        $monitoredJob = $this->jobService->getOrCreateMonitoredJob($job);
        $monitoredJob->addException($exception);

        return $monitoredJob;
    }
}
