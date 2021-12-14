<?php

namespace Aryeo\MonitoredJobs\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Aryeo\MonitoredJobs\Tests\Factories\MonitoredJobFactory;

class MonitoredJob extends Model
{
    use HasFactory;
    use Prunable;

    protected static function newFactory()
    {
        return new MonitoredJobFactory();
    }

    public $guarded = ['id'];

    protected $dateFormat = 'Y-m-d H:i:s.u';

    public $casts = [
        'payload' => 'array',
    ];

    public function prunable()
    {
        return static::where('created_at', '<=', now()->subDays(config('monitored-jobs.prune_after_days')));
    }

    protected function pruning()
    {
        $this->meta()->delete();
        $this->exceptions()->delete();
    }

    public function unserializedPayload()
    {
        return unserialize($this->payload['data']['command'] ?? null);
    }

    public function addMeta(string $type, $value): MonitoredJobMeta
    {
        return $this->meta()
            ->create([
                'type' => $type,
                'value' => $value,
            ]);
    }

    public function addException(string $exception): MonitoredJobException
    {
        return $this->exceptions()->create(['exception' => $exception]);
    }

    public function latestMeta(string $type, $value = null): ?MonitoredJobMeta
    {
        return $this->meta
            ->where('type', $type)
            ->when($value, fn ($query) => $query->where('value', $value))
            ->last();
    }

    public function getDuration(): ?string
    {
        $processing = $this->latestMeta(MonitoredJobMeta::TYPE_STATUS, 'processing') ?? null;
        $finished = $this->latestMeta(MonitoredJobMeta::TYPE_STATUS, 'processed')
            ?? $this->latestMeta(MonitoredJobMeta::TYPE_STATUS, 'failed')
            ?? null;

        if ($processing && $finished) {
            return $finished->created_at->diffForHumans($processing->created_at, true);
        }

        return null;
    }

    public function scopeSearch(Builder $query, $search = null): void
    {
        $query->when($search, fn ($query) => $query->where('name', 'ilike', DB::raw(DB::getPdo()->quote("%$search%"))));
    }

    public function scopeHasTags(Builder $query, array $tags = []): void
    {
        if (empty($tags)) {
            return;
        }

        $query->when(filled($tags), fn ($query) => $query->whereHas('meta', fn ($query) => $query->tags($tags)));
    }

    public function scopeStatus(Builder $query, string $status): void
    {
        $query->whereExists(
            fn ($query) => $query
                ->from('monitored_job_meta')
                ->where('type', MonitoredJobMeta::TYPE_STATUS)
                ->where('value', $status)
                ->whereColumn('monitored_job_id', 'monitored_jobs.id')
                ->latest()
                ->limit(1)
        );
    }

    public function meta(): HasMany
    {
        return $this->hasMany(MonitoredJobMeta::class);
    }

    public function exceptions(): HasMany
    {
        return $this->hasMany(MonitoredJobException::class);
    }
}
