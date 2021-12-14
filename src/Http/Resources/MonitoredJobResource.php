<?php

namespace Aryeo\MonitoredJobs\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use Aryeo\MonitoredJobs\Models\MonitoredJobMeta;

class MonitoredJobResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'connection' => $this->connection,
            'queue' => $this->queue,
            'job_details' => json_encode($this->payload, JSON_PRETTY_PRINT),
            'payload' => json_encode($this->unserializedPayload(), JSON_PRETTY_PRINT),
            'created_at' => $this->created_at,
            'duration' => $this->resource->getDuration(),
            'statuses' => MonitoredJobMetaResource::collection($this->meta->where('type', MonitoredJobMeta::TYPE_STATUS))->toArray($request),
            'tags' => MonitoredJobMetaResource::collection($this->meta->where('type', MonitoredJobMeta::TYPE_TAG))->toArray($request),
            'events' => MonitoredJobMetaResource::collection($this->meta->where('type', MonitoredJobMeta::TYPE_EVENT))->toArray($request),
            'current_status' => Str::ucfirst($this->latestMeta('status')?->value),
            'exceptions' => $this->whenLoaded('exceptions'),
        ];
    }
}
