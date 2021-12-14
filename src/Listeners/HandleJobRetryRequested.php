<?php

namespace Aryeo\MonitoredJobs\Listeners;

use Illuminate\Contracts\Queue\Job;
use Illuminate\Queue\Events\JobRetryRequested;

class HandleJobRetryRequested extends MonitoredJobHandler
{
    public function handle(JobRetryRequested $event)
    {
        if ($this->isEnabled($event->job)) {
            /** @var Job */
            $job = $event->job;

            $this->trackEvent($job, 'retry_requested');
        }
    }
}
