<?php

namespace Aryeo\MonitoredJobs\Listeners;

use Illuminate\Queue\Events\JobQueued;

class HandleJobQueued extends MonitoredJobHandler
{
    public function handle(JobQueued $event)
    {
        if ($this->isEnabled($event->job)) {
            $this->jobService->createQueuedJob($event);
        }
    }
}
