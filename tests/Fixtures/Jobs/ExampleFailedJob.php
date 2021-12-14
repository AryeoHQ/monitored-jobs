<?php

namespace Aryeo\MonitoredJobs\Tests\Fixtures\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Aryeo\MonitoredJobs\Tests\Fixtures\Models\User;

class ExampleFailedJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $tries = 1;

    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function handle()
    {
        throw new \Exception('Failing this job!');
    }
}
