<?php

namespace Aryeo\MonitoredJobs\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Aryeo\MonitoredJobs\Http\Resources\MonitoredJobResource;
use Aryeo\MonitoredJobs\Models\MonitoredJob;
use Aryeo\MonitoredJobs\Models\MonitoredJobMeta;
use Aryeo\MonitoredJobs\Services\MonitoredJobService;

class IndexMonitoredJobsController extends Controller
{
    public function __construct(public MonitoredJobService $service)
    {
    }

    public function __invoke(Request $request)
    {
        return Inertia::render('Index', [
            'input' => $request->input(),
            'tags' => fn () => MonitoredJobMeta::getValuesForType(MonitoredJobMeta::TYPE_TAG),
            'statuses' => fn () => MonitoredJobMeta::getValuesForType(MonitoredJobMeta::TYPE_STATUS),
            'monitoredJobs' => fn () => MonitoredJobResource::collection(
                MonitoredJob::with('meta')
                    ->when($request->search, fn ($query, $term) => $query->search($term))
                    ->when($request->tags, fn ($query, $tags) => $query->hasTags($tags))
                    ->when($request->status, fn ($query, $status) => $query->status($status))
                    ->orderByDesc('created_at')
                    ->orderByDesc('id')
                    ->get()
            )->toArray($request),
        ]);
    }
}
