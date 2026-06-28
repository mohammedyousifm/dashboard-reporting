@extends('layouts.app')
@section('title', __('ui.pg_dashboard'))
@section('page-title', __('ui.pg_dashboard'))
@section('page-subtitle', \Carbon\Carbon::create($currentYear, $currentMonth, 1)->format('F Y') . ' — ' . __('ui.pg_dashboard_sub'))

@section('topbar-actions')
<form class="d-flex gap-2 no-print" method="GET" action="{{ route('dashboard') }}">
    <select name="month" class="form-select form-select-sm" style="width:115px">
        @for($m=1; $m<=12; $m++)
            <option value="{{ $m }}" {{ $m == $currentMonth ? 'selected' : '' }}>{{ \Carbon\Carbon::create(0,$m,1)->format('F') }}</option>
        @endfor
    </select>
    <select name="year" class="form-select form-select-sm" style="width:85px">
        @for($y=date('Y')-5; $y<=date('Y'); $y++)
            <option value="{{ $y }}" {{ $y == $currentYear ? 'selected' : '' }}>{{ $y }}</option>
        @endfor
    </select>
    <button class="btn btn-sm btn-primary">{{ __('ui.btn_go') }}</button>
</form>
@endsection

@section('content')

<!-- KPI Row -->
<div class="row g-3 mb-4">
    @php
    $kpis = [
        ['label'=> __('ui.kpi_tasks_completed'),   'value'=> $tasksCompleted,        'icon'=>'bi-check-circle-fill', 'color'=>'#d1fae5','ic'=>'#059669', 'accent'=>'#10b981'],
        ['label'=> __('ui.kpi_in_progress'),       'value'=> $activeTasks,           'icon'=>'bi-arrow-repeat',      'color'=>'#fef3c7','ic'=>'#d97706', 'accent'=>'#f59e0b'],
        ['label'=> __('ui.kpi_active_projects'),   'value'=> $activeProjects,        'icon'=>'bi-kanban-fill',       'color'=>'#dbeafe','ic'=>'#2563eb', 'accent'=>'#3b82f6'],
        ['label'=> __('ui.kpi_companies'),         'value'=> $companiesServed,       'icon'=>'bi-building-fill',     'color'=>'#fce7f3','ic'=>'#db2777', 'accent'=>'#ec4899'],
        ['label'=> __('ui.kpi_achievements'),      'value'=> $achievementsThisMonth, 'icon'=>'bi-trophy-fill',       'color'=>'#fef9c3','ic'=>'#ca8a04', 'accent'=>'#eab308'],
    ];
    @endphp
    @foreach($kpis as $k)
    <div class="col-6 col-md-4 col-xl-2">
        <div class="kpi-card" style="--kpi-accent:{{ $k['accent'] }}">
            <div class="kpi-icon" style="background:{{ $k['color'] }};color:{{ $k['ic'] }}">
                <i class="bi {{ $k['icon'] }}"></i>
            </div>
            <div class="kpi-value">{{ $k['value'] }}</div>
            <div class="kpi-label">{{ $k['label'] }}</div>
        </div>
    </div>
    @endforeach
</div>

<!-- Charts Row -->
<div class="row g-3 mb-3">
    <div class="col-lg-8">
        <div class="chart-card h-100">
            <div class="card-title">
                <i class="bi bi-bar-chart-fill me-2" style="color:var(--primary)"></i>{{ __('ui.sec_tasks_by_month') }} — {{ $currentYear }}
            </div>
            <canvas id="monthlyTaskChart" height="95"></canvas>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="chart-card h-100">
            <div class="card-title">
                <i class="bi bi-pie-chart-fill me-2" style="color:#d97706"></i>{{ __('ui.sec_task_status') }}
            </div>
            @if($tasksByStatus->isEmpty())
                <div class="chart-empty"><i class="bi bi-check2-circle"></i><span>{{ __('ui.empty_tasks') }}</span></div>
            @else
                <canvas id="taskStatusChart" height="195"></canvas>
            @endif
        </div>
    </div>
</div>

<!-- Mid Row -->
<div class="row g-3 mb-3">
    <div class="col-lg-7">
        <div class="table-card h-100">
            <div class="table-card-header">
                <span class="tc-title"><i class="bi bi-building me-2" style="color:#db2777"></i>{{ __('ui.sec_projects_company') }}</span>
                <a href="{{ route('projects.index') }}" class="btn btn-xs btn-outline-primary">{{ __('ui.btn_view_all') }}</a>
            </div>
            <table class="table table-sm mb-0">
                <thead><tr>
                    <th>{{ __('ui.th_company') }}</th>
                    <th>{{ __('ui.th_active_projects') }}</th>
                    <th style="width:150px">{{ __('ui.th_distribution') }}</th>
                </tr></thead>
                <tbody>
                @forelse($projectsByCompany as $company)
                <tr>
                    <td class="fw-semibold">{{ $company->name }}</td>
                    <td><span class="badge bg-primary bg-opacity-10 text-primary">{{ $company->projects_count }}</span></td>
                    <td>
                        <div class="progress" style="height:5px;border-radius:4px">
                            <div class="progress-bar bg-primary" style="width:{{ $projectsByCompany->max('projects_count') > 0 ? ($company->projects_count / $projectsByCompany->max('projects_count') * 100) : 0 }}%;border-radius:4px"></div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="table-empty"><i class="bi bi-kanban"></i>{{ __('ui.empty_active_projects') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="table-card h-100">
            <div class="table-card-header">
                <span class="tc-title"><i class="bi bi-clock me-2" style="color:#d97706"></i>{{ __('ui.sec_due_7_days') }}</span>
                <a href="{{ route('tasks.index') }}" class="btn btn-xs btn-outline-primary">{{ __('ui.btn_all_tasks') }}</a>
            </div>
            <table class="table table-sm mb-0">
                <thead><tr>
                    <th>{{ __('ui.th_task') }}</th>
                    <th>{{ __('ui.th_project') }}</th>
                    <th>{{ __('ui.th_due') }}</th>
                </tr></thead>
                <tbody>
                @forelse($upcomingTasks as $t)
                <tr>
                    <td class="fw-semibold" style="max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="{{ $t->title }}">{{ $t->title }}</td>
                    <td style="font-size:.8rem;color:#64748b">{{ $t->project?->name ?? '—' }}</td>
                    <td style="font-size:.8rem" class="{{ $t->due_date->isPast() ? 'text-danger fw-bold' : 'text-muted' }}">{{ $t->due_date->format('M d') }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="table-empty"><i class="bi bi-calendar-check"></i>{{ __('ui.empty_upcoming') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Recent Tasks -->
<div class="table-card">
    <div class="table-card-header">
        <span class="tc-title"><i class="bi bi-list-task me-2" style="color:var(--primary)"></i>{{ __('ui.sec_recent_tasks') }}</span>
        <a href="{{ route('tasks.index') }}" class="btn btn-xs btn-outline-primary">{{ __('ui.btn_view_all') }}</a>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr>
                <th>{{ __('ui.th_task') }}</th>
                <th>{{ __('ui.th_project') }}</th>
                <th>{{ __('ui.th_company') }}</th>
                <th>{{ __('ui.th_priority') }}</th>
                <th>{{ __('ui.th_status') }}</th>
                <th>{{ __('ui.th_due') }}</th>
            </tr></thead>
            <tbody>
            @forelse($recentTasks as $t)
            <tr>
                <td class="fw-semibold">{{ $t->title }}</td>
                <td style="font-size:.82rem;color:#64748b">{{ $t->project?->name ?? '—' }}</td>
                <td style="font-size:.82rem;color:#64748b">{{ $t->project?->company?->name ?? '—' }}</td>
                <td>
                    @php $pa=['high'=>['#fee2e2','#991b1b'],'medium'=>['#fef3c7','#92400e'],'low'=>['#d1fae5','#065f46']] @endphp
                    @if(isset($pa[$t->priority]))
                    <span class="badge" style="background:{{ $pa[$t->priority][0] }};color:{{ $pa[$t->priority][1] }}">{{ __('ui.priority_'.$t->priority) }}</span>
                    @endif
                </td>
                <td><span class="badge status-{{ $t->status }}">{{ __('ui.status_'.str_replace('-','_',$t->status)) }}</span></td>
                <td style="font-size:.82rem;color:#64748b">{{ $t->due_date?->format('M d, Y') ?? '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="table-empty"><i class="bi bi-check2-square"></i>{{ __('ui.empty_tasks') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
Chart.defaults.font  = { family: "'Cairo','Segoe UI',sans-serif", size: 12 };
Chart.defaults.color = '#64748b';

new Chart(document.getElementById('monthlyTaskChart'), {
    type: 'bar',
    data: {
        labels: @json($monthlyTaskTrend['labels']),
        datasets: [{
            label: '{{ __('ui.kpi_tasks_completed') }}',
            data: @json($monthlyTaskTrend['data']),
            backgroundColor: 'rgba(79,70,229,.8)',
            borderRadius: 7,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#f1f5f9' } },
            x: { grid: { display: false } }
        }
    }
});

@if($tasksByStatus->isNotEmpty())
const sData = @json($tasksByStatus);
new Chart(document.getElementById('taskStatusChart'), {
    type: 'doughnut',
    data: {
        labels: sData.map(t => ({ pending: '{{ __('ui.status_pending') }}', in_progress: '{{ __('ui.status_in_progress') }}', completed: '{{ __('ui.status_completed') }}', cancelled: '{{ __('ui.status_cancelled') }}' })[t.status] || t.status),
        datasets: [{ data: sData.map(t => t.count), backgroundColor: ['#f59e0b','#6d28d9','#10b981','#ef4444'], borderWidth: 0, hoverOffset: 6 }]
    },
    options: { responsive: true, cutout: '62%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, padding: 12 } } } }
});
@endif
</script>
@endpush
