<?php

namespace App\Services;

use App\Models\MetricData;
use App\Models\SocialAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class MetricDataService
{
    public function getFilteredMetrics($filters)
    {
        $cacheKey = 'metric_data_' . md5(json_encode($filters));
        
        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($filters) {
            $query = MetricData::with(['account.platform'])
                ->whereHas('account', function($q) {
                    $q->whereNull('deleted_at');
                });

            if (Arr::get($filters, 'account_id')) {
                $query->where('social_account_id', $filters['account_id']);
            }

            if (Arr::get($filters, 'date_range')) {
                if (Arr::get($filters, 'date_range') === 'custom' && 
                    Arr::get($filters, 'start_date') && 
                    Arr::get($filters, 'end_date')) {
                    $query->whereBetween('date', [$filters['start_date'], $filters['end_date']]);
                } else {
                    $endDate = now();
                    $startDate = $endDate->copy()->subDays($filters['date_range']);
                    $query->whereBetween('date', [$startDate, $endDate]);
                }
            }

            return $query->orderByDesc('date')
                ->select(['id', 'social_account_id', 'date', 'followers_count', 'engagement_rate', 'reach', 'impressions', 'likes', 'comments', 'shares'])
                ->paginate(10);
        });
    }

    public function getStats()
    {
        return Cache::remember('metric_data_stats', now()->addMinutes(15), function () {
            $latestMetrics = $this->getLatestMetrics();
            
            $stats = $this->calculateBasicStats($latestMetrics);
            $stats = array_merge($stats, $this->calculateGrowthStats($latestMetrics));
            
            return $stats;
        });
    }

    private function getLatestMetrics()
    {
        return DB::table('metric_data as md1')
            ->select('md1.*')
            ->join(DB::raw('(
                SELECT social_account_id, MAX(date) as max_date
                FROM metric_data
                WHERE deleted_at IS NULL
                GROUP BY social_account_id
            ) as md2'), function($join) {
                $join->on('md1.social_account_id', '=', 'md2.social_account_id')
                     ->on('md1.date', '=', 'md2.max_date');
            })
            ->whereNull('md1.deleted_at')
            ->get();
    }

    private function calculateBasicStats($metrics)
    {
        return [
            'total_followers' => $metrics->sum('followers_count'),
            'average_engagement' => round($metrics->avg('engagement_rate'), 2),
            'total_reach' => $metrics->sum('reach'),
            'total_interactions' => $metrics->sum(function($metric) {
                return $metric->likes + $metric->comments + $metric->shares;
            })
        ];
    }

    private function calculateGrowthStats($latestMetrics)
    {
        return [
            'follower_growth' => $this->calculateGrowth($latestMetrics, 'followers_count'),
            'engagement_growth' => $this->calculateGrowth($latestMetrics, 'engagement_rate'),
            'reach_growth' => $this->calculateGrowth($latestMetrics, 'reach'),
            'interactions_growth' => $this->calculateInteractionsGrowth($latestMetrics)
        ];
    }

    private function calculateGrowth($latestMetrics, $field)
    {
        $totalGrowth = 0;
        $accountCount = 0;

        foreach ($latestMetrics as $current) {
            $previous = DB::table('metric_data')
                ->where('social_account_id', $current->social_account_id)
                ->where('date', '<', $current->date)
                ->orderByDesc('date')
                ->first();

            if ($previous && $previous->$field > 0) {
                $growth = (($current->$field - $previous->$field) / $previous->$field) * 100;
                $totalGrowth += $growth;
                $accountCount++;
            }
        }

        return $accountCount > 0 ? round($totalGrowth / $accountCount, 2) : 0;
    }

    private function calculateInteractionsGrowth($latestMetrics)
    {
        $totalGrowth = 0;
        $accountCount = 0;

        foreach ($latestMetrics as $current) {
            $previous = DB::table('metric_data')
                ->where('social_account_id', $current->social_account_id)
                ->where('date', '<', $current->date)
                ->orderByDesc('date')
                ->first();

            if ($previous) {
                $currentInteractions = $current->likes + $current->comments + $current->shares;
                $previousInteractions = $previous->likes + $previous->comments + $previous->shares;

                if ($previousInteractions > 0) {
                    $growth = (($currentInteractions - $previousInteractions) / $previousInteractions) * 100;
                    $totalGrowth += $growth;
                    $accountCount++;
                }
            }
        }

        return $accountCount > 0 ? round($totalGrowth / $accountCount, 2) : 0;
    }

    public function deleteMetric($id)
    {
        DB::beginTransaction();
        try {
            $metric = MetricData::findOrFail($id);
            $metric->delete();
            
            Cache::tags(['metric_data'])->flush();
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting metric', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
} 