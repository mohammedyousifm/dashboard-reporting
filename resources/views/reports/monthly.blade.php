@extends('layouts.app')
@section('title', __('ui.pg_monthly'))
@section('page-title', __('ui.pg_monthly'))
@section('page-subtitle', $start->format('F Y'))

@section('topbar-actions')
<div class="d-flex gap-2 no-print">
    <form class="d-flex gap-2" method="GET">
        <select name="month" class="form-select form-select-sm" style="width:130px">
            @for($m=1; $m<=12; $m++)
                <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>{{ \Carbon\Carbon::create(0,$m,1)->format('F') }}</option>
            @endfor
        </select>
        <select name="year" class="form-select form-select-sm" style="width:88px">
            @for($y=date('Y')-3; $y<=date('Y'); $y++)
                <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
        <button class="btn btn-sm btn-primary">{{ __('ui.btn_go') }}</button>
    </form>
    <a href="{{ route('reports.export', 'monthly') }}?format=pdf&month={{ $month }}&year={{ $year }}" class="btn btn-sm btn-outline-danger"><i class="bi bi-filetype-pdf me-1"></i>PDF</a>
    <a href="{{ route('reports.export', 'monthly') }}?format=excel&month={{ $month }}&year={{ $year }}" class="btn btn-sm btn-outline-success"><i class="bi bi-file-earmark-excel me-1"></i>Excel</a>
    <button onclick="window.print()" class="btn btn-sm btn-outline-secondary"><i class="bi bi-printer me-1"></i>{{ __('ui.btn_print') }}</button>
</div>
@endsection

@section('content')

<div class="row g-3 mb-4">
    @php
    $kpis = [
        ['label'=> __('ui.kpi_tasks_completed'),    'value'=> $tasksCompleted,    'icon'=>'bi-check-circle-fill', 'color'=>'#d1fae5','ic'=>'#059669','accent'=>'#10b981'],
        ['label'=> __('ui.kpi_active_tasks'),       'value'=> $activeTasks,       'icon'=>'bi-arrow-repeat',      'color'=>'#fef3c7','ic'=>'#d97706','accent'=>'#f59e0b'],
        ['label'=> __('ui.kpi_projects_involved'),  'value'=> $projectsWorked,    'icon'=>'bi-kanban-fill',       'color'=>'#dbeafe','ic'=>'#2563eb','accent'=>'#3b82f6'],
        ['label'=> __('ui.kpi_completed_projects'), 'value'=> $completedProjects, 'icon'=>'bi-flag-fill',         'color'=>'#d1fae5','ic'=>'#059669','accent'=>'#10b981'],
        ['label'=> __('ui.kpi_companies'),          'value'=> $companiesServed,   'icon'=>'bi-building-fill',     'color'=>'#fce7f3','ic'=>'#db2777','accent'=>'#ec4899'],
        ['label'=> __('ui.kpi_achievements'),       'value'=> $achievementsCount, 'icon'=>'bi-trophy-fill',       'color'=>'#fef9c3','ic'=>'#ca8a04','accent'=>'#eab308'],
    ];
    @endphp
    @foreach($kpis as $k)
    <div class="col-6 col-md-4 col-xl-2">
        <div class="kpi-card" style="--kpi-accent:{{ $k['accent'] }}">
            <div class="kpi-icon" style="background:{{ $k['color'] }};color:{{ $k['ic'] }}"><i class="bi {{ $k['icon'] }}"></i></div>
            <div class="kpi-value">{{ $k['value'] }}</div>
            <div class="kpi-label">{{ $k['label'] }}</div>
        </div>
    </div>
    @endforeach
</div>

<div class="summary-box mb-4" style="background:#d1fae5;border-left:4px solid #059669">
    <div class="sb-icon" style="background:#059669;color:#fff"><i class="bi bi-stars"></i></div>
    <div>
        <div class="sb-label" style="color:#059669">{{ __('ui.sec_my_monthly_summary') }} — {{ $start->format('F Y') }}</div>
        <p>{{ $summary }}</p>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-lg-7">
        <div class="chart-card">
            <div class="card-title"><i class="bi bi-bar-chart me-2" style="color:#059669"></i>{{ __('ui.sec_weekly_tasks') }} — {{ $start->format('F Y') }}</div>
            @if(collect($weeklyTaskTrend)->sum('tasks') == 0)
                <div class="chart-empty"><i class="bi bi-bar-chart"></i><span>{{ __('ui.empty_no_tasks_completed') }}</span></div>
            @else
                <canvas id="weeklyTaskChart" height="135"></canvas>
            @endif
        </div>
    </div>
    <div class="col-lg-5">
        <div class="chart-card h-100">
            <div class="card-title"><i class="bi bi-pie-chart me-2" style="color:#d97706"></i>{{ __('ui.sec_tasks_priority') }}</div>
            @if($tasksByPriority->isEmpty())
                <div class="chart-empty"><i class="bi bi-pie-chart"></i><span>{{ __('ui.empty_no_task_data') }}</span></div>
            @else
                <canvas id="priorityChart" height="180"></canvas>
            @endif
        </div>
    </div>
</div>

@if($achievementList->isNotEmpty())
<div class="table-card">
    <div class="table-card-header">
        <span class="tc-title"><i class="bi bi-trophy me-2" style="color:#ca8a04"></i>{{ __('ui.sec_achievements_month') }}</span>
    </div>
    <table class="table mb-0">
        <thead><tr>
            <th>{{ __('ui.th_achievement') }}</th>
            <th>{{ __('ui.th_project') }}</th>
            <th>{{ __('ui.th_type') }}</th>
            <th>{{ __('ui.th_date') }}</th>
        </tr></thead>
        <tbody>
        @foreach($achievementList as $a)
        <tr>
            <td class="fw-semibold">{{ $a->title }}</td>
            <td style="color:#64748b;font-size:.83rem">{{ $a->project?->name ?? '—' }}</td>
            <td><span class="badge status-active">{{ ucfirst($a->type) }}</span></td>
            <td style="color:#64748b;font-size:.83rem">{{ $a->achieved_date->format('M d, Y') }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection

@push('scripts')
<script>
@if(collect($weeklyTaskTrend)->sum('tasks') > 0)
new Chart(document.getElementById('weeklyTaskChart'), {
    type: 'bar',
    data: {
        labels: @json(collect($weeklyTaskTrend)->pluck('label')),
        datasets: [{ label: '{{ __('ui.kpi_tasks_completed') }}', data: @json(collect($weeklyTaskTrend)->pluck('tasks')), backgroundColor: 'rgba(5,150,105,.78)', borderRadius: 7, borderSkipped: false }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#f1f5f9' } }, x: { grid: { display: false } } } }
});
@endif
@if($tasksByPriority->isNotEmpty())
const prData = @json($tasksByPriority);
new Chart(document.getElementById('priorityChart'), {
    type: 'doughnut',
    data: {
        labels: prData.map(p => ({ low: '{{ __('ui.priority_low') }}', medium: '{{ __('ui.priority_medium') }}', high: '{{ __('ui.priority_high') }}' })[p.priority] || p.priority),
        datasets: [{ data: prData.map(p => p.count), backgroundColor: prData.map(p => ({ low: '#059669', medium: '#d97706', high: '#dc2626' })[p.priority] || '#4f46e5'), borderWidth: 0, hoverOffset: 6 }]
    },
    options: { responsive: true, cutout: '62%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, padding: 12 } } } }
});
@endif
</script>
@endpush
