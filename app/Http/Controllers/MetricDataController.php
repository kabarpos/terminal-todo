<?php

namespace App\Http\Controllers;

use App\Models\MetricData;
use App\Models\SocialAccount;
use App\Models\SocialPlatform;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MetricDataImport;
use App\Exports\MetricDataExport;
use App\Exports\MetricDataTemplateExport;

class MetricDataController extends Controller
{
    public function index(Request $request)
    {
        $query = MetricData::with(['account.platform'])
            ->when($request->account_id, function ($query, $accountId) {
                return $query->where('social_account_id', $accountId);
            })
            ->when($request->date_range, function ($query, $range) {
                if ($range === 'custom' && request()->filled(['start_date', 'end_date'])) {
                    return $query->whereBetween('date', [request('start_date'), request('end_date')]);
                }
                
                $endDate = now();
                $startDate = $endDate->copy()->subDays($range);
                return $query->whereBetween('date', [$startDate, $endDate]);
            });

        $metrics = $query->latest('date')->paginate(10);

        // Calculate stats
        $stats = [
            'total_followers' => $query->sum('followers_count'),
            'follower_growth' => $this->calculateGrowth($query->clone(), 'followers_count'),
            'average_engagement' => round($query->avg('engagement_rate'), 2),
            'engagement_growth' => $this->calculateGrowth($query->clone(), 'engagement_rate'),
            'total_reach' => $query->sum('reach'),
            'reach_growth' => $this->calculateGrowth($query->clone(), 'reach'),
            'total_interactions' => $query->sum('likes') + $query->sum('comments') + $query->sum('shares'),
            'interactions_growth' => $this->calculateInteractionsGrowth($query->clone())
        ];

        return Inertia::render('MetricData/Index', [
            'metrics' => $metrics,
            'accounts' => SocialAccount::active()->with('platform')->get(),
            'stats' => $stats,
            'filters' => $request->only(['account_id', 'date_range', 'start_date', 'end_date'])
        ]);
    }

    private function calculateGrowth($query, $field)
    {
        $current = $query->latest('date')->first()?->$field ?? 0;
        $previous = $query->latest('date')->skip(1)->first()?->$field ?? 0;

        if ($previous == 0) return 0;
        return round((($current - $previous) / $previous) * 100, 2);
    }

    private function calculateInteractionsGrowth($query)
    {
        $current = $query->latest('date')->first();
        $previous = $query->latest('date')->skip(1)->first();

        if (!$current || !$previous) return 0;

        $currentTotal = $current->likes + $current->comments + $current->shares;
        $previousTotal = $previous->likes + $previous->comments + $previous->shares;

        if ($previousTotal == 0) return 0;
        return round((($currentTotal - $previousTotal) / $previousTotal) * 100, 2);
    }

    public function create()
    {
        return inertia('MetricData/Create', [
            'accounts' => SocialAccount::active()->with('platform')->get(),
            'auth' => [
                'user' => auth()->user()
            ]
        ]);
    }

    public function store(Request $request)
    {
        // Validasi dasar dengan range
        $validated = $request->validate([
            'social_account_id' => 'required|exists:social_accounts,id',
            'date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($request) {
                    // Cek apakah kombinasi social_account_id dan date sudah ada
                    $exists = MetricData::where('social_account_id', $request->social_account_id)
                        ->whereDate('date', $value)
                        ->exists();
                    
                    if ($exists) {
                        $fail('Data metrik untuk akun dan tanggal ini sudah ada.');
                    }
                }
            ],
            'followers_count' => 'required|integer|min:0|max:1000000000',
            'engagement_rate' => 'required|numeric|between:0,100',
            'reach' => [
                'required',
                'integer',
                'min:0',
                'max:1000000000',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value > $request->impressions) {
                        $fail('Reach tidak boleh lebih besar dari impressions.');
                    }
                }
            ],
            'impressions' => 'required|integer|min:0|max:1000000000',
            'likes' => [
                'required',
                'integer',
                'min:0',
                'max:1000000000',
                function ($attribute, $value, $fail) use ($request) {
                    $totalEngagement = $value + ($request->comments ?? 0) + ($request->shares ?? 0);
                    if ($totalEngagement > $request->impressions) {
                        $fail('Total engagement (likes + comments + shares) tidak boleh lebih besar dari impressions.');
                    }
                }
            ],
            'comments' => 'required|integer|min:0|max:1000000000',
            'shares' => 'required|integer|min:0|max:1000000000'
        ], [
            'followers_count.max' => 'Jumlah followers terlalu besar (max: 1 miliar)',
            'reach.max' => 'Jumlah reach terlalu besar (max: 1 miliar)',
            'impressions.max' => 'Jumlah impressions terlalu besar (max: 1 miliar)',
            'likes.max' => 'Jumlah likes terlalu besar (max: 1 miliar)',
            'comments.max' => 'Jumlah comments terlalu besar (max: 1 miliar)',
            'shares.max' => 'Jumlah shares terlalu besar (max: 1 miliar)',
        ]);

        try {
            MetricData::create($validated);
            return redirect()->route('metric-data.index')
                ->with('message', 'Data metrik berhasil disimpan');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data. ' . $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $metricData = MetricData::with(['account' => function($query) {
            $query->withTrashed();
        }, 'account.platform'])->findOrFail($id);

        return Inertia::render('MetricData/Show', [
            'metricData' => $metricData,
            'auth' => [
                'user' => auth()->user()
            ]
        ]);
    }

    public function edit(MetricData $metricData)
    {
        return Inertia::render('MetricData/Edit', [
            'metricData' => $metricData,
            'accounts' => SocialAccount::active()->with('platform')->get()
        ]);
    }

    public function update(Request $request, MetricData $metricData)
    {
        // Validasi dasar
        $validated = $request->validate([
            'social_account_id' => 'required|exists:social_accounts,id',
            'date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($request, $metricData) {
                    // Cek apakah kombinasi social_account_id dan date sudah ada (kecuali untuk data ini sendiri)
                    $exists = MetricData::where('social_account_id', $request->social_account_id)
                        ->whereDate('date', $value)
                        ->where('id', '!=', $metricData->id)
                        ->exists();
                    
                    if ($exists) {
                        $fail('Data metrik untuk akun dan tanggal ini sudah ada.');
                    }
                }
            ],
            'followers_count' => 'required|integer',
            'engagement_rate' => 'required|numeric|between:0,100',
            'reach' => 'required|integer',
            'impressions' => 'required|integer',
            'likes' => 'required|integer',
            'comments' => 'required|integer',
            'shares' => 'required|integer'
        ]);

        try {
            $metricData->update($validated);
            return redirect()->route('metric-data.index')
                ->with('message', 'Data metrik berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat memperbarui data. ' . $e->getMessage()]);
        }
    }

    public function destroy(MetricData $metricData)
    {
        $metricData->delete();

        return redirect()->route('metric-data.index')
            ->with('message', 'Data metrik berhasil dihapus');
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
        $query = MetricData::with(['account.platform'])
            ->whereBetween('date', [$startDate, $endDate])
            ->when($request->platform_id, function ($query, $platformId) {
                return $query->whereHas('account', function ($q) use ($platformId) {
                    $q->where('platform_id', $platformId);
                });
            })
            ->when($request->account_id, function ($query, $accountId) {
                return $query->where('social_account_id', $accountId);
            });

        // Mengambil data per platform
        $platformMetrics = $platforms->map(function ($platform) use ($query) {
            $platformQuery = $query->clone()->whereHas('account', function ($q) use ($platform) {
                $q->where('platform_id', $platform->id);
            });

            return [
                'id' => $platform->id,
                'name' => $platform->name,
                'icon' => $platform->icon,
                'followers' => $platformQuery->latest('date')->first()?->followers_count ?? 0,
                'followersTrend' => $this->calculateGrowth($platformQuery, 'followers_count'),
                'engagementRate' => round($platformQuery->avg('engagement_rate'), 2),
                'engagementTrend' => $this->calculateGrowth($platformQuery, 'engagement_rate'),
                'reach' => $platformQuery->sum('reach'),
                'reachTrend' => $this->calculateGrowth($platformQuery, 'reach'),
                'interactions' => $platformQuery->sum('likes') + $platformQuery->sum('comments') + $platformQuery->sum('shares'),
                'interactionsTrend' => $this->calculateInteractionsGrowth($platformQuery)
            ];
        });

        return Inertia::render('SocialMedia/Analytics', [
            'platforms' => $platforms,
            'accounts' => $accounts,
            'analytics' => [
                'platforms' => $platformMetrics,
                'dates' => $query->pluck('date'),
                'followers' => $query->pluck('followers_count'),
                'engagement_rates' => $query->pluck('engagement_rate'),
                'reach' => $query->pluck('reach'),
                'impressions' => $query->pluck('impressions'),
                'likes' => $query->pluck('likes'),
                'comments' => $query->pluck('comments'),
                'shares' => $query->pluck('shares')
            ]
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new MetricDataImport, $request->file('file'));
            return redirect()->back()->with('message', 'Data berhasil diimport');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = collect($failures)->map(function ($failure) {
                return "Baris {$failure->row()}: {$failure->errors()[0]}";
            })->join('<br>');
            
            return redirect()->back()->withErrors(['error' => "Gagal import data:<br>{$errors}"]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal import data: ' . $e->getMessage()]);
        }
    }

    public function export(Request $request)
    {
        $request->validate([
            'account_id' => 'nullable|exists:social_accounts,id',
            'start_date' => 'required_with:end_date|date',
            'end_date' => 'required_with:start_date|date|after_or_equal:start_date'
        ]);

        $fileName = 'metric-data-' . now()->format('Y-m-d') . '.xlsx';
        
        return Excel::download(
            new MetricDataExport(
                $request->account_id,
                $request->start_date,
                $request->end_date
            ),
            $fileName
        );
    }

    public function downloadTemplate()
    {
        $fileName = 'template-import-metric-data.xlsx';
        
        return Excel::download(new MetricDataTemplateExport(), $fileName);
    }
} 