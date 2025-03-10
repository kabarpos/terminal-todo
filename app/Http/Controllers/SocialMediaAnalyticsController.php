<?php

namespace App\Http\Controllers;

use App\Models\MetricData;
use App\Models\Platform;
use App\Models\SocialAccount;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SocialMediaAnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index(Request $request)
    {
        // Get date range
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays($request->date_range ?? 30);

        if ($request->date_range === 'custom' && $request->start_date && $request->end_date) {
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
        }

        // Base query
        $query = MetricData::query()
            ->when($request->platform_id, function($q) use ($request) {
                return $q->whereHas('account', fn($q) => $q->where('platform_id', $request->platform_id));
            })
            ->when($request->account_id, function($q) use ($request) {
                return $q->where('social_account_id', $request->account_id);
            })
            ->whereBetween('date', [$startDate, $endDate]);

        // Get previous period data for comparison
        $previousStartDate = (clone $startDate)->subDays($endDate->diffInDays($startDate));
        $previousEndDate = (clone $startDate)->subDay();

        $previousQuery = MetricData::query()
            ->when($request->platform_id, function($q) use ($request) {
                return $q->whereHas('account', fn($q) => $q->where('platform_id', $request->platform_id));
            })
            ->when($request->account_id, function($q) use ($request) {
                return $q->where('social_account_id', $request->account_id);
            })
            ->whereBetween('date', [$previousStartDate, $previousEndDate]);

        // Calculate metrics
        $currentMetrics = $query->get();
        $previousMetrics = $previousQuery->get();

        $analytics = [
            'total_followers' => $currentMetrics->last()?->followers_count ?? 0,
            'followers_growth' => $this->calculateGrowth(
                $previousMetrics->last()?->followers_count,
                $currentMetrics->last()?->followers_count
            ),
            'avg_engagement' => $currentMetrics->avg('engagement_rate') ?? 0,
            'engagement_growth' => $this->calculateGrowth(
                $previousMetrics->avg('engagement_rate'),
                $currentMetrics->avg('engagement_rate')
            ),
            'total_reach' => $currentMetrics->sum('reach'),
            'reach_growth' => $this->calculateGrowth(
                $previousMetrics->sum('reach'),
                $currentMetrics->sum('reach')
            ),
            'total_interactions' => $currentMetrics->sum('likes') + $currentMetrics->sum('comments') + $currentMetrics->sum('shares'),
            'interactions_growth' => $this->calculateGrowth(
                $previousMetrics->sum('likes') + $previousMetrics->sum('comments') + $previousMetrics->sum('shares'),
                $currentMetrics->sum('likes') + $currentMetrics->sum('comments') + $currentMetrics->sum('shares')
            ),
            'total_likes' => $currentMetrics->sum('likes'),
            'total_comments' => $currentMetrics->sum('comments'),
            'total_shares' => $currentMetrics->sum('shares'),
        ];

        return Inertia::render('SocialMedia/Analytics', [
            'analytics' => $analytics,
            'platforms' => Platform::all(),
            'accounts' => SocialAccount::with('platform')->get(),
        ]);
    }

    private function calculateGrowth($previous, $current)
    {
        if (!$previous || !$current) return 0;
        return round((($current - $previous) / $previous) * 100, 2);
    }
}
