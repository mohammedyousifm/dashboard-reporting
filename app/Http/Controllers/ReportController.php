<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Project;
use App\Models\Task;
use App\Models\Achievement;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\WeeklyReportExport;
use App\Exports\MonthlyReportExport;
use App\Exports\YearlyReportExport;

class ReportController extends Controller
{
    public function index()
    {
        $companies  = Company::orderBy('name')->get();
        $projects   = Project::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        return view('reports.index', compact('companies', 'projects', 'categories'));
    }

    public function weekly(Request $request)
    {
        return view('reports.weekly', $this->buildWeeklyData($request));
    }

    public function monthly(Request $request)
    {
        return view('reports.monthly', $this->buildMonthlyData($request));
    }

    public function yearly(Request $request)
    {
        return view('reports.yearly', $this->buildYearlyData($request));
    }

    public function export(Request $request, string $type)
    {
        $format   = $request->get('format', 'pdf');
        $filename = $type . '-report-' . now()->format('Y-m-d');

        $data = match($type) {
            'weekly'  => $this->buildWeeklyData($request),
            'monthly' => $this->buildMonthlyData($request),
            'yearly'  => $this->buildYearlyData($request),
            default   => abort(404),
        };

        if ($format === 'excel') {
            $export = match($type) {
                'weekly'  => new WeeklyReportExport($data),
                'monthly' => new MonthlyReportExport($data),
                'yearly'  => new YearlyReportExport($data),
            };
            return Excel::download($export, "$filename.xlsx");
        }

        return Pdf::loadView("reports.pdf.$type", $data)->download("$filename.pdf");
    }

    // ──────────────────────────────── DATA BUILDERS ─────────────────────────

    private function buildWeeklyData(Request $request): array
    {
        $date    = $request->get('week_start')
            ? Carbon::parse($request->get('week_start'))->startOfWeek(Carbon::SUNDAY)
            : Carbon::now()->startOfWeek(Carbon::SUNDAY);
        $weekEnd = $date->copy()->endOfWeek(Carbon::SATURDAY);

        $tasksCompleted  = $this->countCompletedBetween($date, $weekEnd);
        $tasksInProgress = Task::where('status', 'in_progress')->count();
        $achievementsCount = Achievement::whereBetween('achieved_date', [$date->toDateString(), $weekEnd->toDateString()])->count();

        $taskProjectIds  = $this->taskProjectIdsBetween($date, $weekEnd);
        $projectsWorked  = $taskProjectIds->count();
        $companiesServed = Project::whereIn('id', $taskProjectIds)->distinct('company_id')->count('company_id');

        $taskList = Task::with(['project.company', 'category'])
            ->where(function ($q) use ($date, $weekEnd) {
                $q->whereBetween('due_date', [$date->toDateString(), $weekEnd->toDateString()])
                  ->orWhere(function ($q2) use ($date, $weekEnd) {
                      $q2->whereNotNull('completed_date')
                         ->whereBetween('completed_date', [$date->toDateString(), $weekEnd->toDateString()]);
                  });
            })->orderBy('due_date')->get();

        $achievementList = Achievement::with('project')
            ->whereBetween('achieved_date', [$date->toDateString(), $weekEnd->toDateString()])
            ->get();

        $summary = $this->generateSummary('weekly', $date, $weekEnd, $tasksCompleted, $projectsWorked, 0, $companiesServed, $achievementsCount);

        return compact('date', 'weekEnd', 'tasksCompleted', 'tasksInProgress', 'projectsWorked', 'companiesServed', 'achievementsCount', 'taskList', 'achievementList', 'summary');
    }

    private function buildMonthlyData(Request $request): array
    {
        $month = max(1, min(12, (int) $request->get('month', now()->month)));
        $year  = (int) $request->get('year', now()->year);
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        $tasksCompleted    = $this->countCompletedBetween($start, $end);
        $activeTasks       = Task::where('status', 'in_progress')->count();
        $completedProjects = Project::whereBetween('end_date', [$start->toDateString(), $end->toDateString()])->where('status', 'completed')->count();
        $activeProjects    = Project::where('status', 'active')->count();
        $achievementsCount = Achievement::whereBetween('achieved_date', [$start->toDateString(), $end->toDateString()])->count();

        $taskProjectIds  = $this->taskProjectIdsBetween($start, $end);
        $projectsWorked  = $taskProjectIds->count();
        $companiesServed = Project::whereIn('id', $taskProjectIds)->distinct('company_id')->count('company_id');

        $weeklyTaskTrend = [];
        $cursor = $start->copy()->startOfWeek(Carbon::SUNDAY);
        if ($cursor->lt($start)) $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $wEnd = $cursor->copy()->endOfWeek(Carbon::SATURDAY);
            if ($wEnd->gt($end)) $wEnd = $end->copy();
            $weeklyTaskTrend[] = [
                'label' => $cursor->format('M d') . ' – ' . $wEnd->format('M d'),
                'tasks' => $this->countCompletedBetween($cursor->copy(), $wEnd->copy()),
            ];
            $cursor->addWeek()->startOfWeek(Carbon::SUNDAY);
        }

        $tasksByPriority = Task::select('priority', DB::raw('count(*) as count'))->groupBy('priority')->get();

        $achievementList = Achievement::with('project')
            ->whereBetween('achieved_date', [$start->toDateString(), $end->toDateString()])
            ->get();

        $summary = $this->generateSummary('monthly', $start, $end, $tasksCompleted, $projectsWorked, $completedProjects, $companiesServed, $achievementsCount);

        return compact(
            'month', 'year', 'start', 'end',
            'tasksCompleted', 'activeTasks', 'completedProjects', 'activeProjects',
            'projectsWorked', 'companiesServed', 'achievementsCount',
            'weeklyTaskTrend', 'tasksByPriority', 'achievementList', 'summary'
        );
    }

    private function buildYearlyData(Request $request): array
    {
        $year  = (int) $request->get('year', now()->year);
        $start = Carbon::create($year, 1, 1)->startOfYear();
        $end   = $start->copy()->endOfYear();

        $tasksCompleted    = $this->countCompletedForYear($year);
        $completedProjects = Project::whereYear('end_date', $year)->where('status', 'completed')->count();
        $activeProjects    = Project::where('status', 'active')->count();
        $totalProjects     = Project::count();
        $companiesServed   = Company::has('projects')->count();
        $achievementsCount = Achievement::whereYear('achieved_date', $year)->count();

        $monthlyTrend = [];
        for ($m = 1; $m <= 12; $m++) {
            $mStart = Carbon::create($year, $m, 1)->startOfMonth();
            $mEnd   = $mStart->copy()->endOfMonth();
            $monthlyTrend[] = [
                'label'           => Carbon::create($year, $m, 1)->format('M'),
                'tasks_completed' => $this->countCompletedBetween($mStart, $mEnd),
            ];
        }

        $tasksByStatus = Task::select('status', DB::raw('count(*) as count'))->groupBy('status')->get();

        $projectStatusBreakdown = Project::select('status', DB::raw('count(*) as count'))->groupBy('status')->get();

        $summary = $this->generateSummary('yearly', $start, $end, $tasksCompleted, $totalProjects, $completedProjects, $companiesServed, $achievementsCount);

        return compact(
            'year', 'tasksCompleted', 'totalProjects', 'completedProjects', 'activeProjects',
            'companiesServed', 'achievementsCount', 'monthlyTrend', 'tasksByStatus',
            'projectStatusBreakdown', 'summary'
        );
    }

    // ──────────────────────────────── HELPERS ───────────────────────────────

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

    private function countCompletedForYear(int $year): int
    {
        return Task::where('status', 'completed')
            ->where(function ($q) use ($year) {
                $q->where(function ($q2) use ($year) {
                    $q2->whereNotNull('completed_date')->whereYear('completed_date', $year);
                })->orWhere(function ($q2) use ($year) {
                    $q2->whereNull('completed_date')->whereYear('updated_at', $year);
                });
            })->count();
    }

    private function taskProjectIdsBetween(Carbon $start, Carbon $end)
    {
        return Task::where(function ($q) use ($start, $end) {
            $q->whereBetween('due_date', [$start->toDateString(), $end->toDateString()])
              ->orWhere(function ($q2) use ($start, $end) {
                  $q2->whereNotNull('completed_date')
                     ->whereBetween('completed_date', [$start->toDateString(), $end->toDateString()]);
              });
        })->distinct('project_id')->pluck('project_id');
    }

    private function generateSummary(string $period, $start, $end, int $tasks, int $totalProjects, int $completedProjects, int $companies, int $achievements): string
    {
        $periodLabel = match($period) {
            'weekly'  => 'the week of ' . $start->format('F d') . ' to ' . $end->format('F d, Y'),
            'monthly' => $start->format('F Y'),
            'yearly'  => 'the year ' . $start->year,
        };

        $projectText = "I worked on $totalProjects project" . ($totalProjects !== 1 ? 's' : '');
        if ($companies > 0) {
            $projectText .= " across $companies " . ($companies !== 1 ? 'companies' : 'company');
        }

        if ($completedProjects > 0) {
            $completionText = ", completed $completedProjects project" . ($completedProjects !== 1 ? 's' : '') .
                              " and $tasks " . ($tasks !== 1 ? 'tasks' : 'task');
        } else {
            $completionText = ", and completed $tasks " . ($tasks !== 1 ? 'tasks' : 'task');
        }

        $achievementText = $achievements > 0
            ? ", and recorded $achievements achievement" . ($achievements !== 1 ? 's' : '')
            : '';

        return "During $periodLabel, $projectText$completionText$achievementText.";
    }
}
