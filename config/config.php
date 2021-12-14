<?php

return [
    'enabled' => env('MONITORED_JOBS_ENABLED', true),
    'prune_after_days' => 14,
    'include_jobs' => null,
    'exclude_jobs' => null,
];
