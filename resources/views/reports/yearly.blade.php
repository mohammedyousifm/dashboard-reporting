@extends('layouts.app')
@section('title', __('ui.pg_yearly'))
@section('page-title', __('ui.pg_yearly'))
@section('page-subtitle', 'Year ' . $year . ' — ' . __('ui.annual_summary'))

@section('topbar-actions')
<div class="d-flex gap-2 no-print">
    <form class="d-flex gap-2" method="GET">
        <select name="year" class="form-select form-select-sm" style="width:95px">
            @for($y=date('Y')-5; $y<=date('Y'); $y++)
                <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
        <button class="btn btn-sm btn-primary">{{ __('ui.btn_go') }}</button>
    </form>
    <a href="{{ route('reports.export', 'yearly') }}?format=pdf&year={{ $year }}" class="btn btn-sm btn-outline-danger"><i class="bi bi-filetype-pdf me-1"></i>PDF</a>
    <a href="{{ route('reports.export', 'yearly') }}?format=excel&year={{ $year }}" class="btn btn-sm btn-outline-success"><i class="bi bi-file-earmark-excel me-1"></i>Excel</a>
    <button onclick="window.print()" class="btn btn-sm btn-outline-secondary"><i class="bi bi-printer me-1"></i>{{ __('ui.btn_print') }}</button>
</div>
@endsection

@section('content')

<div class="row g-3 mb-4">
    @php
    $kpis = [
        ['label'=> __('ui.kpi_tasks_completed'),    'value'=> number_format($tasksCompleted), 'icon'=>'bi-check-circle-fill', 'color'=>'#d1fae5','ic'=>'#059669','accent'=>'#10b981'],
        ['label'=> __('ui.kpi_total_projects'),     'value'=> $totalProjects,                 'icon'=>'bi-kanban-fill',       'color'=>'#dbeafe','ic'=>'#2563eb','accent'=>'#3b82f6'],
        ['label'=> __('ui.kpi_completed_projects'), 'value'=> $completedProjects,             'icon'=>'bi-flag-fill',         'color'=>'#d1fae5','ic'=>'#059669','accent'=>'#10b981'],
        ['label'=> __('ui.kpi_active_projects'),    'value'=> $activeProjects,                'icon'=>'bi-diagram-3-fill',    'color'=>'#e0f2fe','ic'=>'#0284c7','accent'=>'#0ea5e9'],
        ['label'=> __('ui.kpi_companies'),          'value'=> $companiesServed,               'icon'=>'bi-building-fill',     'color'=>'#fce7f3','ic'=>'#db2777','accent'=>'#ec4899'],
        ['label'=> __('ui.kpi_achievements'),       'value'=> $achievementsCount,             'icon'=>'bi-trophy-fill',       'color'=>'#fef9c3','ic'=>'#ca8a04','accent'=>'#eab308'],
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

<div class="summary-box mb-4" style="background:#dbeafe;border-left:4px solid #2563eb">
    <div class="sb-icon" style="background:#2563eb;color:#fff"><i class="bi bi-stars"></i></div>
    <div>
        <div class="sb-label" style="color:#2563eb">{{ __('ui.sec_my_year_review') }} — {{ $year }}</div>
        <p style="font-size:.94rem">{{ $summary }}</p>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-lg-8">
        <div class="chart-card">
            <div class="card-title"><i class="bi bi-bar-chart-fill me-2" style="color:var(--primary)"></i>{{ __('ui.sec_tasks_by_month') }} — {{ $year }}</div>
            <canvas id="monthlyTaskChart" height="110"></canvas>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="chart-card h-100">
            <div class="card-title"><i class="bi bi-pie-chart-fill me-2" style="color:#d97706"></i>{{ __('ui.sec_task_status') }}</div>
            @if($tasksByStatus->isEmpty())
                <div class="chart-empty"><i class="bi bi-check2-circle"></i><span>{{ __('ui.empty_tasks') }}</span></div>
            @else
                <canvas id="taskStatusChart" height="195"></canvas>
            @endif
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="table-card">
            <div class="table-card-header">
                <span class="tc-title">{{ __('ui.sec_monthly_breakdown') }} — {{ $year }}</span>
            </div>
            <table class="table mb-0">
                <thead><tr>
                    <th>{{ __('ui.th_month') }}</th>
                    <th>{{ __('ui.kpi_tasks_completed') }}</th>
                    <th style="width:180px">{{ __('ui.th_distribution') }}</th>
                </tr></thead>
                <tbody>
                @php $maxT = collect($monthlyTrend)->max('tasks_completed') ?: 1; @endphp
                @foreach($monthlyTrend as $mt)
                <tr>
                    <td class="fw-semibold">{{ $mt['label'] }}</td>
                    <td>{{ $mt['tasks_completed'] ?: '—' }}</td>
                    <td>
                        @if($mt['tasks_completed'] > 0)
                        <div class="progress" style="height:6px;border-radius:4px">
                            <div class="progress-bar bg-primary" style="width:{{ $mt['tasks_completed'] / $maxT * 100 }}%;border-radius:4px"></div>
                        </div>
                        @endif
                    </td>
                </tr>
                @endforeach
                </tbody>
                <tfoot><tr class="table-light fw-bold"><td>{{ __('ui.total') }}</td><td>{{ $tasksCompleted }}</td><td></td></tr></tfoot>
            </table>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="table-card h-100">
            <div class="table-card-header">
                <span class="tc-title">{{ __('ui.sec_project_status') }}</span>
            </div>
            <table class="table mb-0">
                <thead><tr>
                    <th>{{ __('ui.th_status') }}</th>
                    <th>{{ __('ui.th_count') }}</th>
                    <th style="width:130px">{{ __('ui.th_distribution') }}</th>
                </tr></thead>
                <tbody>
                @php $totalPrj = $projectStatusBreakdown->sum('count') ?: 1; @endphp
                @forelse($projectStatusBreakdown as $ps)
                <tr>
                    <td><span class="badge status-{{ $ps->status }}">{{ __('ui.status_'.$ps->status) }}</span></td>
                    <td class="fw-semibold">{{ $ps->count }}</td>
                    <td>
                        <div class="progress" style="height:6px;border-radius:4px">
                            <div class="progress-bar bg-primary" style="width:{{ $ps->count / $totalPrj * 100 }}%;border-radius:4px"></div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="table-empty"><i class="bi bi-kanban"></i>{{ __('ui.empty_no_projects') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const monthlyData = @json($monthlyTrend);
new Chart(document.getElementById('monthlyTaskChart'), {
    type: 'bar',
    data: {
        labels: monthlyData.map(m => m.label),
        datasets: [{ label: '{{ __('ui.kpi_tasks_completed') }}', data: monthlyData.map(m => m.tasks_completed), backgroundColor: 'rgba(79,70,229,.8)', borderRadius: 7, borderSkipped: false }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#f1f5f9' } }, x: { grid: { display: false } } } }
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
