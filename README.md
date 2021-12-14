# Monitored Jobs for Laravel

## Overview

This package tracks the status and history of your queued jobs by hooking into the events that Laravel fires for its queue system.

We store this data in a database table, `monitored_jobs`.

## Installation

`composer require aryeo/monitored-jobs`

## Configuration

You can optionally publish the configuration file, which allows you to configure:

- if monitoring is enabled
- which jobs to monitor
- the number of days to keep monitored job history before pruning

To publish the config file:

`php artisan vendor:publish --tag=monitored-jobs`

### Controlling monitoring

To control whether _any_ job is monitored, set the `monitored-jobs.enabled` configuration value.

There are two configuration options for filtering which jobs are monitored: `include_jobs` and `exclude_jobs`. The default value for both config options is `null`, which will include any job class.

- `'include_jobs' => null` means we are not setting the config option, so all jobs are monitored
- `'include_jobs' => []` means we are setting the config option to include no jobs, meaning no jobs are monitored

#### Including and excluding the same job class

The `exclude_jobs` option will be checked first and will skip monitoring the job if it is found to be excluded.

### Pruning Monitored Jobs

The `MonitoredJob` model is setup to use Laravel's model pruning: https://laravel.com/docs/8.x/eloquent#pruning-models.

In order for the models to be pruned, you must setup the command via the scheduler:

```
protected function schedule(Schedule $schedule)
{
    $schedule->command('model:prune')->daily();
}
```

You can adjust how long the monitored job records are kept for via the config file:

```
'prune_after_days' => 14,
```

## Tags

The package will attempt to pull tags off of job classes. It does this by either using the `tags` method on the job class, or by pulling the arguments off of the job's constructor. When pulling the arguments off the constructor:

- Models & collections of models will be serialized as `{$class}_{$KEY}`
- Arrays will be JSON encoded
- Booleans will be converted to "true" / "false" strings
- Strings and numbers will be stored as-is
- Objects will not be stored as tags
