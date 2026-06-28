<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Project;
use App\Models\Task;
use App\Models\Achievement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $now          = Carbon::now();
        $currentMonth = max(1, min(12, (int) $request->get('month', $now->month)));
        $currentYear  = max($now->year - 5, min($now->year + 1, (int) $request->get('year', $now->year)));

        $tasksCompleted = $this->countCompletedBetween(
            Carbon::create($currentYear, $currentMonth, 1)->startOfMonth(),
            Carbon::create($currentYear, $currentMonth, 1)->endOfMonth()
        );

        $activeTasks     = Task::where('status', 'in_progress')->count();
        $activeProjects  = Project::where('status', 'active')->count();
        $companiesServed = Company::where('status', 'active')->count();

        $achievementsThisMonth = Achievement::whereYear('achieved_date', $currentYear)
            ->whereMonth('achieved_date', $currentMonth)
            ->count();

        $monthlyTaskTrend = $this->getMonthlyTaskTrend($currentYear);

        $tasksByStatus = Task::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        $tasksByPriority = Task::select('priority', DB::raw('count(*) as count'))
            ->groupBy('priority')
            ->get();

        $projectsByCompany = Company::withCount(['projects' => function ($q) {
            $q->where('status', 'active');
        }])->having('projects_count', '>', 0)->get();

        $upcomingTasks = Task::with(['project.company', 'category'])
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [$now->toDateString(), $now->copy()->addDays(7)->toDateString()])
            ->orderBy('due_date')
            ->limit(8)
            ->get();

        $recentTasks = Task::with(['project.company', 'category'])
            ->latest('updated_at')
            ->limit(10)
            ->get();

        return view('dashboard.index', compact(
            'tasksCompleted', 'activeTasks', 'activeProjects',
            'companiesServed', 'achievementsThisMonth',
            'monthlyTaskTrend', 'tasksByStatus', 'tasksByPriority',
            'projectsByCompany', 'upcomingTasks', 'recentTasks',
            'currentMonth', 'currentYear', 'now'
        ));
    }

    private function countCompletedBetween(Carbon $start, Carbon $end): int
    {
        return Task::where('status', 'completed')
            ->where(function ($q) use ($start, $end) {
                $q->where(function ($q2) use ($start, $end) {
                    $q2->whereNotNull('completed_date')
                        ->whereBetween('completed_date', [$start->toDateString(), $end->toDateString()]);
                })->orWhere(function ($q2) use ($start, $end) {
                    $q2->whereNull('completed_date')
                        ->whereBetween('updated_at', [$start->startOfDay(), $end->endOfDay()]);
                });
            })->count();
    }

    private function getMonthlyTaskTrend(int $year): array
    {
        $labels = [];
        $data   = [];

        for ($m = 1; $m <= 12; $m++) {
            $mStart = Carbon::create($year, $m, 1)->startOfMonth();
            $mEnd   = $mStart->copy()->endOfMonth();
            $labels[] = Carbon::create($year, $m, 1)->format('M');
            $data[]   = $this->countCompletedBetween($mStart, $mEnd);
        }

        return ['labels' => $labels, 'data' => $data];
    }
}
