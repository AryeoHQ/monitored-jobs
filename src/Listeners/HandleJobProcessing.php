<?php

namespace Aryeo\MonitoredJobs\Listeners;

use Illuminate\Queue\Events\JobProcessing;

class HandleJobProcessing extends MonitoredJobHandler
{
    public function handle(JobProcessing $event)
    {
        if ($this->isEnabled($event->job->resolveName())) {
            $this->trackStatus($event->job, 'processing');
        }
    }
}
