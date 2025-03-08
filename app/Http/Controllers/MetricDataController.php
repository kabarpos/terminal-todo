<?php

namespace App\Http\Controllers;

use App\Models\MetricData;
use App\Models\SocialAccount;
use App\Models\Metric;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use App\Models\SocialPlatform;

class MetricDataController extends Controller
{
    public function index(Request $request)
    {
        $query = MetricData::with(['account.platform', 'metric'])
            ->when($request->account_id, function ($query, $accountId) {
                return $query->where('account_id', $accountId);
            })
            ->when($request->metric_id, function ($query, $metricId) {
                return $query->where('metric_id', $metricId);
            })
            ->when($request->year, function ($query, $year) {
                return $query->where('year', $year);
            })
            ->when($request->month, function ($query, $month) {
                return $query->where('month', $month);
            })
            ->when($request->week, function ($query, $week) {
                return $query->where('week_number', $week);
            });

        $metricData = $query->latest('recorded_at')->paginate(10);

        return Inertia::render('MetricData/Index', [
            'metricData' => $metricData,
            'accounts' => SocialAccount::active()->with('platform')->get(),
            'metrics' => Metric::active()->with('platform')->get(),
            'filters' => $request->only(['account_id', 'metric_id', 'year', 'month', 'week'])
        ]);
    }

    public function create()
    {
        return inertia('MetricData/Create', [
            'platforms' => SocialPlatform::active()->get(),
            'accounts' => SocialAccount::active()->get(),
            'metrics' => Metric::all()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'platform_id' => 'required|exists:social_platforms,id',
            'account_id' => 'required|exists:social_accounts,id',
            'metric_id' => 'required|exists:metrics,id',
            'value' => 'required|numeric',
            'recorded_at' => 'required|date'
        ]);

        MetricData::create($validated);

        return redirect()->back()->with('success', 'Data metrik berhasil disimpan');
    }

    public function show(MetricData $metricData)
    {
        $metricData->load(['account.platform', 'metric']);
        
        return Inertia::render('MetricData/Show', [
            'metricData' => $metricData
        ]);
    }

    public function edit(MetricData $metricData)
    {
        return Inertia::render('MetricData/Edit', [
            'metricData' => $metricData,
            'accounts' => SocialAccount::active()->with('platform')->get(),
            'metrics' => Metric::active()->with('platform')->get()
        ]);
    }

    public function update(Request $request, MetricData $metricData)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:social_accounts,id',
            'metric_id' => 'required|exists:metrics,id',
            'value' => 'required|numeric',
            'recorded_at' => 'required|date',
            'metadata' => 'nullable|array',
        ]);

        $recordedAt = Carbon::parse($validated['recorded_at']);
        
        $validated['year'] = $recordedAt->year;
        $validated['month'] = $recordedAt->month;
        $validated['week_number'] = $recordedAt->week;

        $metricData->update($validated);

        return redirect()->route('metric-data.index')
            ->with('message', 'Data metrik berhasil diperbarui');
    }

    public function destroy(MetricData $metricData)
    {
        $metricData->delete();

        return redirect()->route('metric-data.index')
            ->with('message', 'Data metrik berhasil dihapus');
    }

    public function report(Request $request)
    {
        $query = MetricData::with(['account.platform', 'metric'])
            ->when($request->account_id, function ($query, $accountId) {
                return $query->where('account_id', $accountId);
            })
            ->when($request->platform_id, function ($query, $platformId) {
                return $query->whereHas('account', function ($q) use ($platformId) {
                    $q->where('platform_id', $platformId);
                });
            })
            ->when($request->start_date && $request->end_date, function ($query) use ($request) {
                return $query->betweenDates($request->start_date, $request->end_date);
            });

        $report = $query->get()->groupBy(['account.platform.name', 'metric.name']);

        return Inertia::render('MetricData/Report', [
            'report' => $report,
            'accounts' => SocialAccount::active()->with('platform')->get(),
            'platforms' => SocialPlatform::active()->get(),
            'filters' => $request->only(['account_id', 'platform_id', 'start_date', 'end_date'])
        ]);
    }

    public function analytics(Request $request)
    {
        $platforms = SocialPlatform::active()->get();
        $accounts = SocialAccount::active()->with('platform')->get();

        // Get date range
        $endDate = now();
        $startDate = $endDate->copy()->subDays(30);

        if ($request->filled(['start_date', 'end_date'])) {
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
        }

        // Query base untuk metrik
        $query = MetricData::with(['account.platform', 'metric'])
            ->whereBetween('recorded_at', [$startDate, $endDate])
            ->when($request->platform_id, function ($query, $platformId) {
                return $query->whereHas('account', function ($q) use ($platformId) {
                    $q->where('platform_id', $platformId);
                });
            })
            ->when($request->account_id, function ($query, $accountId) {
                return $query->where('account_id', $accountId);
            });

        // Mengambil data metrik
        $metrics = [
            'followers' => $this->getMetricData($query->clone(), 'followers'),
            'engagement' => $this->getMetricData($query->clone(), 'engagement_rate'),
            'interactions' => $this->getMetricData($query->clone(), 'total_interactions'),
            'reach' => $this->getMetricData($query->clone(), 'reach'),
        ];

        // Mengambil data per platform
        $platformMetrics = $platforms->map(function ($platform) use ($query) {
            $platformQuery = $query->clone()->whereHas('account', function ($q) use ($platform) {
                $q->where('platform_id', $platform->id);
            });

            return [
                'id' => $platform->id,
                'name' => $platform->name,
                'icon' => $platform->icon,
                'followers' => $this->getLatestMetricValue($platformQuery->clone(), 'followers'),
                'followersTrend' => $this->calculateTrend($platformQuery->clone(), 'followers'),
                'engagementRate' => $this->getLatestMetricValue($platformQuery->clone(), 'engagement_rate'),
                'engagementTrend' => $this->calculateTrend($platformQuery->clone(), 'engagement_rate'),
                'interactions' => $this->getLatestMetricValue($platformQuery->clone(), 'total_interactions'),
                'interactionsTrend' => $this->calculateTrend($platformQuery->clone(), 'total_interactions'),
                'reach' => $this->getLatestMetricValue($platformQuery->clone(), 'reach'),
                'reachTrend' => $this->calculateTrend($platformQuery->clone(), 'reach'),
            ];
        });

        $metrics['platforms'] = $platformMetrics;

        return Inertia::render('SocialMedia/Analytics', [
            'platforms' => $platforms,
            'accounts' => $accounts,
            'metrics' => $metrics,
        ]);
    }

    private function getMetricData($query, $metricKey)
    {
        $data = $query->whereHas('metric', function ($q) use ($metricKey) {
            $q->where('key', $metricKey);
        })->orderBy('recorded_at')->get();

        $current = $data->last()?->value ?? 0;
        $previous = $data->reverse()->skip(1)->first()?->value ?? 0;
        $trend = $previous > 0 ? (($current - $previous) / $previous) * 100 : 0;

        return [
            'current' => $current,
            'trend' => round($trend, 2),
            'history' => $data->map(function ($item) {
                return [
                    'recorded_at' => $item->recorded_at,
                    'value' => $item->value,
                ];
            }),
        ];
    }

    private function getLatestMetricValue($query, $metricKey)
    {
        return $query->whereHas('metric', function ($q) use ($metricKey) {
            $q->where('key', $metricKey);
        })->latest('recorded_at')->first()?->value ?? 0;
    }

    private function calculateTrend($query, $metricKey)
    {
        $data = $query->whereHas('metric', function ($q) use ($metricKey) {
            $q->where('key', $metricKey);
        })->orderBy('recorded_at', 'desc')->take(2)->get();

        if ($data->count() < 2) return 0;

        $current = $data->first()->value;
        $previous = $data->last()->value;

        return $previous > 0 ? round((($current - $previous) / $previous) * 100, 2) : 0;
    }
} 