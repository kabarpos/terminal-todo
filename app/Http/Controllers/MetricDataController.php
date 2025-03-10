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

    public function edit($id)
    {
        try {
            $metricData = MetricData::with(['account.platform'])->findOrFail($id);

            return Inertia::render('MetricData/Edit', [
                'metricData' => $metricData,
                'accounts' => SocialAccount::active()->with('platform')->get(),
                'auth' => [
                    'user' => auth()->user()
                ]
            ]);
        } catch (\Exception $e) {
            return redirect()->route('metric-data.index')
                ->with('error', 'Data metrik tidak ditemukan');
        }
    }

    public function update(Request $request, $id)
    {
        $metricData = MetricData::findOrFail($id);

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
            'followers_count' => 'required|integer|min:0',
            'engagement_rate' => 'required|numeric|between:0,100',
            'reach' => [
                'required',
                'integer',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value > $request->impressions) {
                        $fail('Reach tidak boleh lebih besar dari impressions.');
                    }
                }
            ],
            'impressions' => 'required|integer|min:0',
            'likes' => [
                'required',
                'integer',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    $totalEngagement = $value + ($request->comments ?? 0) + ($request->shares ?? 0);
                    if ($totalEngagement > $request->impressions) {
                        $fail('Total engagement (likes + comments + shares) tidak boleh lebih besar dari impressions.');
                    }
                }
            ],
            'comments' => 'required|integer|min:0',
            'shares' => 'required|integer|min:0'
        ]);

        try {
            DB::beginTransaction();
            
            $metricData->update($validated);
            
            DB::commit();
            
            return redirect()->route('metric-data.index')
                ->with('message', 'Data metrik berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            
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
        $startDate = $endDate->copy()->subDays($request->date_range ?: 30);

        if ($request->date_range === 'custom' && $request->filled(['start_date', 'end_date'])) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
        } else if ($request->date_range) {
            $startDate = $endDate->copy()->subDays($request->date_range);
        }

        // Query base untuk metrik - tanpa filter platform/account dulu
        $baseQuery = MetricData::with(['account.platform'])
            ->whereBetween('date', [$startDate, $endDate]);

        // Get metrics data
        $metrics = $baseQuery->orderBy('date', 'asc')->get();

        // Debug log
        \Log::info('Analytics Query', [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'metrics_count' => $metrics->count(),
            'request_filters' => $request->all()
        ]);

        // Get all dates in range
        $dates = [];
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dates[] = $currentDate->format('Y-m-d');
            $currentDate->addDay();
        }

        // Debug log
        \Log::info('Dates Range', [
            'dates' => $dates,
            'count' => count($dates)
        ]);

        // Jika tidak ada data, gunakan data dummy untuk testing
        if ($metrics->isEmpty()) {
            // Generate dummy data untuk testing
            $dummyData = collect($dates)->map(function($date) {
                return [
                    'date' => $date,
                    'followers_count' => rand(1000, 2000),
                    'engagement_rate' => rand(1, 5),
                    'reach' => rand(500, 1000),
                    'impressions' => rand(1000, 2000),
                    'likes' => rand(100, 200),
                    'comments' => rand(10, 50),
                    'shares' => rand(5, 20)
                ];
            });

            return Inertia::render('SocialMedia/Analytics', [
                'platforms' => $platforms,
                'accounts' => $accounts,
                'analytics' => [
                    'platforms' => [],
                    'dates' => $dates,
                    'followers' => $dummyData->pluck('followers_count')->values(),
                    'engagement_rates' => $dummyData->pluck('engagement_rate')->values(),
                    'reach' => $dummyData->pluck('reach')->values(),
                    'impressions' => $dummyData->pluck('impressions')->values(),
                    'likes' => $dummyData->pluck('likes')->values(),
                    'comments' => $dummyData->pluck('comments')->values(),
                    'shares' => $dummyData->pluck('shares')->values()
                ],
                'insights' => [
                    [
                        'type' => 'info',
                        'title' => 'Data Simulasi',
                        'description' => 'Data yang ditampilkan adalah data simulasi karena belum ada data aktual.'
                    ]
                ]
            ]);
        }

        // Process metrics data
        $processedData = [
            'followers' => array_fill_keys($dates, 0),
            'engagement_rates' => array_fill_keys($dates, 0),
            'reach' => array_fill_keys($dates, 0),
            'impressions' => array_fill_keys($dates, 0),
            'likes' => array_fill_keys($dates, 0),
            'comments' => array_fill_keys($dates, 0),
            'shares' => array_fill_keys($dates, 0)
        ];

        // Fill with actual data
        foreach ($metrics as $metric) {
            $date = $metric->date->format('Y-m-d');
            if (isset($processedData['followers'][$date])) {
                $processedData['followers'][$date] += $metric->followers_count;
                $processedData['engagement_rates'][$date] += $metric->engagement_rate;
                $processedData['reach'][$date] += $metric->reach;
                $processedData['impressions'][$date] += $metric->impressions;
                $processedData['likes'][$date] += $metric->likes;
                $processedData['comments'][$date] += $metric->comments;
                $processedData['shares'][$date] += $metric->shares;
            }
        }

        // Debug log
        \Log::info('Processed Data', [
            'sample_followers' => array_slice($processedData['followers'], 0, 5),
            'sample_engagement' => array_slice($processedData['engagement_rates'], 0, 5)
        ]);

        // Mengambil data per platform
        $platformMetrics = $platforms->map(function ($platform) use ($metrics) {
            $platformData = $metrics->filter(function ($metric) use ($platform) {
                return $metric->account->platform_id === $platform->id;
            });

            if ($platformData->isEmpty()) {
                return [
                    'id' => $platform->id,
                    'name' => $platform->name,
                    'icon' => $platform->icon,
                    'followers' => rand(1000, 2000), // Dummy data untuk testing
                    'followersTrend' => rand(-10, 10),
                    'engagementRate' => rand(1, 5),
                    'engagementTrend' => rand(-10, 10),
                    'reach' => rand(500, 1000),
                    'reachTrend' => rand(-10, 10),
                    'interactions' => rand(100, 500),
                    'interactionsTrend' => rand(-10, 10)
                ];
            }

            $latestData = $platformData->sortByDesc('date')->first();
            $previousData = $platformData->sortByDesc('date')->skip(1)->first();

            return [
                'id' => $platform->id,
                'name' => $platform->name,
                'icon' => $platform->icon,
                'followers' => $latestData->followers_count,
                'followersTrend' => $this->calculateTrend($latestData->followers_count, $previousData?->followers_count),
                'engagementRate' => round($platformData->avg('engagement_rate'), 2),
                'engagementTrend' => $this->calculateTrend($latestData->engagement_rate, $previousData?->engagement_rate),
                'reach' => $platformData->sum('reach'),
                'reachTrend' => $this->calculateTrend($latestData->reach, $previousData?->reach),
                'interactions' => $platformData->sum('likes') + $platformData->sum('comments') + $platformData->sum('shares'),
                'interactionsTrend' => 0
            ];
        });

        // Generate insights
        $insights = [
            [
                'type' => 'info',
                'title' => 'Ringkasan Data',
                'description' => sprintf('Menampilkan data dari %s sampai %s', 
                    $startDate->format('d M Y'), 
                    $endDate->format('d M Y'))
            ]
        ];

        if ($metrics->isNotEmpty()) {
            $totalFollowers = $metrics->max('followers_count');
            $insights[] = [
                'type' => 'success',
                'title' => 'Total Followers',
                'description' => sprintf('Total followers saat ini: %s', number_format($totalFollowers))
            ];

            $avgEngagement = $metrics->avg('engagement_rate');
            $insights[] = [
                'type' => $avgEngagement > 2 ? 'success' : 'info',
                'title' => 'Rata-rata Engagement',
                'description' => sprintf('Rata-rata engagement rate: %.1f%%', $avgEngagement)
            ];
        }

        return Inertia::render('SocialMedia/Analytics', [
            'platforms' => $platforms,
            'accounts' => $accounts,
            'analytics' => [
                'platforms' => $platformMetrics->values(),
                'dates' => $dates,
                'followers' => array_values($processedData['followers']),
                'engagement_rates' => array_values($processedData['engagement_rates']),
                'reach' => array_values($processedData['reach']),
                'impressions' => array_values($processedData['impressions']),
                'likes' => array_values($processedData['likes']),
                'comments' => array_values($processedData['comments']),
                'shares' => array_values($processedData['shares'])
            ],
            'insights' => $insights
        ]);
    }

    private function calculateTrend($current, $previous)
    {
        if (!$previous || $previous == 0) return 0;
        return round((($current - $previous) / $previous) * 100, 2);
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