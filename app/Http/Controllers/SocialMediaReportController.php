<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SocialMediaReport;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SocialMediaReportsExport;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Platform;

class SocialMediaReportController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('permission:view-social-media-report')->except(['create', 'store', 'edit', 'update', 'destroy']);
        $this->middleware('permission:manage-social-media-report')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        return Inertia::render('SocialMediaReports/Index', [
            'reports' => SocialMediaReport::with(['category', 'creator', 'platform'])
                ->orderBy('posting_date', 'desc')
                ->get(),
            'categories' => Category::where('type', 'content')->get(),
            'users' => User::permission('manage-social-media-report')->get(),
            'platforms' => Platform::all(),
        ]);
    }

    public function create()
    {
        return Inertia::render('SocialMediaReports/Create', [
            'categories' => Category::where('type', 'content')->get(),
            'platforms' => Platform::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'posting_date' => 'required|date_format:Y-m-d',
            'category_id' => 'required|exists:categories,id',
            'platform_id' => 'required|exists:platforms,id',
            'url' => 'required|url',
        ]);

        SocialMediaReport::create($validated);

        return redirect()->route('social-media-reports.index')
            ->with('message', 'Laporan berhasil ditambahkan');
    }

    public function edit(SocialMediaReport $socialMediaReport)
    {
        return Inertia::render('SocialMediaReports/Edit', [
            'report' => $socialMediaReport->load(['category', 'creator', 'platform']),
            'categories' => Category::where('type', 'content')->get(),
            'platforms' => Platform::all(),
        ]);
    }

    public function update(Request $request, SocialMediaReport $socialMediaReport)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'posting_date' => 'required|date_format:Y-m-d',
            'category_id' => 'required|exists:categories,id',
            'platform_id' => 'required|exists:platforms,id',
            'url' => 'required|url',
        ]);

        $socialMediaReport->update($validated);

        return redirect()->route('social-media-reports.index')
            ->with('message', 'Laporan berhasil diperbarui');
    }

    public function destroy(SocialMediaReport $socialMediaReport)
    {
        $socialMediaReport->delete();

        return redirect()->route('social-media-reports.index')
            ->with('message', 'Laporan berhasil dihapus');
    }

    public function export(Request $request)
    {
        return Excel::download(new SocialMediaReportsExport($request->all()), 'social-media-reports.xlsx');
    }
} 