<?php

namespace Aryeo\MonitoredJobs\Tests\Fixtures\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Aryeo\MonitoredJobs\Tests\Fixtures\Models\User;

class ExampleSuccessfulJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $user;
    public $foo;

    public function __construct(User $user, $foo)
    {
        $this->user = $user;
        $this->foo = $foo;
    }

    public function handle()
    {
        // no-op
    }
}
