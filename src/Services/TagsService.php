<?php

/**
 * Mostly copied from Laravel Horizon.
 */

namespace Aryeo\MonitoredJobs\Services;

use Illuminate\Broadcasting\BroadcastEvent;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Events\CallQueuedListener;
use Illuminate\Mail\SendQueuedMailable;
use Illuminate\Notifications\SendQueuedNotifications;
use Illuminate\Support\Collection;
use ReflectionClass;
use ReflectionProperty;
use stdClass;

class TagsService
{
    /**
     * Determine the tags for the given job.
     *
     * @param mixed $job
     */
    public static function for($job): Collection
    {
        if ($tags = static::extractExplicitTags($job)) {
            return collect($tags);
        }

        return static::tagsFor(static::targetsFor($job));
    }

    /**
     * Extract tags from job object.
     *
     * @param mixed $job
     * @return array
     */
    public static function extractExplicitTags($job)
    {
        return $job instanceof CallQueuedListener
                    ? static::tagsForListener($job)
                    : static::explicitTags(static::targetsFor($job));
    }

    /**
     * Determine tags for the given queued listener.
     *
     * @param mixed $job
     * @return array
     */
    protected static function tagsForListener($job)
    {
        return collect(
            [static::extractListener($job), static::extractEvent($job)]
        )->map(function ($job) {
            return static::for($job);
        })->collapse()->unique()->toArray();
    }

    /**
     * Determine tags for the given job.
     *
     * @return mixed
     */
    protected static function explicitTags(array $jobs)
    {
        return collect($jobs)->map(function ($job) {
            return method_exists($job, 'tags') ? $job->tags() : [];
        })->collapse()->unique()->all();
    }

    /**
     * Get the actual target for the given job.
     *
     * @param mixed $job
     * @return array
     */
    public static function targetsFor($job)
    {
        switch (true) {
            case $job instanceof BroadcastEvent:
                return [$job->event];
            case $job instanceof CallQueuedListener:
                return [static::extractEvent($job)];
            case $job instanceof SendQueuedMailable:
                return [$job->mailable];
            case $job instanceof SendQueuedNotifications:
                return [$job->notification];
            default:
                return [$job];
        }
    }

    /**
     * Get the models from the given object.
     */
    public static function tagsFor(array $targets): Collection
    {
        $tags = [];

        foreach ($targets as $target) {
            $targetClass = new ReflectionClass($target);

            if (!$targetClass->hasMethod('__construct')) {
                continue;
            }

            $constructorParams = collect(
                $targetClass
                    ->getMethod('__construct')
                    ->getParameters()
            )->pluck('name');

            $constructorParams = collect(
                (new ReflectionClass($target))
                ->getMethod('__construct')
                ->getParameters()
            )->pluck('name');

            $tags[] = collect(
                $targetClass->getProperties()
            )->map(function ($property) use ($constructorParams, $target) {
                if (!$constructorParams->contains($property->name)) {
                    return;
                }

                $value = static::getValue($property, $target);

                if ($value instanceof Model) {
                    return [
                        get_class($value).':'.$value->getKey(),
                    ];
                }

                if ($value instanceof EloquentCollection) {
                    return $value->map(function ($model) {
                        return get_class($model).':'.$model->getKey();
                    })->all();
                }

                if (!is_object($value) && $property->class === get_class($target)) {
                    if (is_array($value)) {
                        $value = json_encode($value);
                    }
                    if (is_bool($value)) {
                        $value = $value ? 'true' : 'false';
                    }

                    return [
                        "{$property->name}:{$value}",
                    ];
                }
            })->collapse()->filter()->all();
        }

        return collect($tags)->collapse()->unique();
    }

    /**
     * Get the value of the given ReflectionProperty.
     *
     * @param mixed $target
     */
    protected static function getValue(ReflectionProperty $property, $target)
    {
        if (method_exists($property, 'isInitialized') &&
            !$property->isInitialized($target)) {
            return;
        }

        return $property->getValue($target);
    }

    /**
     * Extract the listener from a queued job.
     *
     * @param mixed $job
     * @return mixed
     */
    protected static function extractListener($job)
    {
        return (new ReflectionClass($job->class))->newInstanceWithoutConstructor();
    }

    /**
     * Extract the event from a queued job.
     *
     * @param mixed $job
     * @return mixed
     */
    protected static function extractEvent($job)
    {
        return isset($job->data[0]) && is_object($job->data[0])
                        ? $job->data[0]
                        : new stdClass();
    }
}
