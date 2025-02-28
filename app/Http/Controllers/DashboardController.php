<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\EditorialCalendar;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Get task statistics
        $taskStats = Task::select('status', DB::raw('count(*) as total'))
            ->whereIn('status', ['draft', 'in_progress', 'completed', 'cancelled'])
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status')
            ->toArray();

        // Get monthly events statistics
        $now = Carbon::now();
        $monthlyEvents = [];
        
        // Get events for current month and next 5 months
        for ($i = 0; $i < 6; $i++) {
            $month = $now->copy()->addMonths($i);
            $startOfMonth = $month->copy()->startOfMonth();
            $endOfMonth = $month->copy()->endOfMonth();
            
            $events = EditorialCalendar::whereBetween('publish_date', [$startOfMonth, $endOfMonth])->get();
            
            // Calculate status
            $status = 'upcoming';
            if ($month->month === $now->month) {
                $status = 'in_progress';
            } elseif ($month->lt($now)) {
                $status = 'completed';
            }
            
            $monthlyEvents[] = [
                'name' => $month->format('F Y'),
                'total' => $events->count(),
                'status' => $status
            ];
        }

        return Inertia::render('Dashboard/Index', [
            'taskStats' => [
                'draft' => $taskStats['draft'] ?? 0,
                'in_progress' => $taskStats['in_progress'] ?? 0,
                'completed' => $taskStats['completed'] ?? 0,
                'cancelled' => $taskStats['cancelled'] ?? 0
            ],
            'monthlyEvents' => $monthlyEvents
        ]);
    }
} 