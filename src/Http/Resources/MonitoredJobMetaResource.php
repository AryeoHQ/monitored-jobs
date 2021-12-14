<?php

namespace Aryeo\MonitoredJobs\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MonitoredJobMetaResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'value' => $this->value,
            'created_at' => $this->created_at->format($this->dateFormat),
        ];
    }
}
