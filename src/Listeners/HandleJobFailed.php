<?php

namespace Aryeo\MonitoredJobs\Listeners;

use Illuminate\Queue\Events\JobFailed;

class HandleJobFailed extends MonitoredJobHandler
{
    public function handle(JobFailed $event)
    {
        if ($this->isEnabled($event->job->resolveName())) {
            $this->trackStatus($event->job, 'failed');
        }
    }
}
