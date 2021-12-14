<?php

namespace Aryeo\MonitoredJobs\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoredJobMeta extends Model
{
    use HasFactory;

    public $table = 'monitored_job_meta';

    public $dateFormat = 'Y-m-d H:i:s.u';

    public const TYPE_TAG = 'tag';
    public const TYPE_STATUS = 'status';
    public const TYPE_EVENT = 'event';

    public $guarded = ['id'];

    public static function getValuesForType(string $type): array
    {
        return static::where('type', $type)
            ->groupBy('value')
            ->select('value')
            ->pluck('value')
            ->toArray();
    }

    public function scopeTags(Builder $query, array $tags): void
    {
        $query->where('type', static::TYPE_TAG)
            ->whereIn('value', $tags);
    }
}
