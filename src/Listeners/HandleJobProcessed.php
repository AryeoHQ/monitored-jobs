<?php

namespace Aryeo\MonitoredJobs\Listeners;

use Illuminate\Queue\Events\JobProcessed;

class HandleJobProcessed extends MonitoredJobHandler
{
    public function handle(JobProcessed $event)
    {
        if ($this->isEnabled($event->job->resolveName())) {
            $this->trackStatus($event->job, 'processed');
        }
    }
}
