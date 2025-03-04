<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SocialMediaReport;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SocialMediaReportsExport;

class SocialMediaReportController extends Controller
{
    public function index()
    {
        return Inertia::render('SocialMediaReports/Index', [
            'reports' => SocialMediaReport::with(['category', 'user'])
                ->orderBy('posting_date', 'desc')
                ->get(),
            'categories' => Category::where('type', 'content')->get(),
            'users' => User::permission('create content')->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('SocialMediaReports/Create', [
            'categories' => Category::where('type', 'content')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'posting_date' => 'required|date',
            'category_id' => 'required|exists:categories,id',
            'url' => 'required|url',
        ]);

        $report = new SocialMediaReport($validated);
        $report->user_id = auth()->id();
        $report->save();

        return redirect()->route('social-media-reports.index')
            ->with('message', 'Laporan berhasil ditambahkan');
    }

    public function edit(SocialMediaReport $socialMediaReport)
    {
        return Inertia::render('SocialMediaReports/Edit', [
            'report' => $socialMediaReport->load(['category', 'user']),
            'categories' => Category::where('type', 'content')->get(),
        ]);
    }

    public function update(Request $request, SocialMediaReport $socialMediaReport)
    {
        $validated = $request->validate([
            'posting_date' => 'required|date',
            'category_id' => 'required|exists:categories,id',
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