<?php

namespace Aryeo\MonitoredJobs\Listeners;

use Illuminate\Queue\Events\JobExceptionOccurred;

class HandleJobExceptionOccurred extends MonitoredJobHandler
{
    public function handle(JobExceptionOccurred $event)
    {
        if ($this->isEnabled($event->job->resolveName())) {
            $this->trackEvent($event->job, 'exception_occurred');
            $this->recordException($event->job, $event->exception);
        }
    }
}
