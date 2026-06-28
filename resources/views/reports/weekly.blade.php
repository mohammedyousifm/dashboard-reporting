@extends('layouts.app')
@section('title', __('ui.pg_weekly'))
@section('page-title', __('ui.pg_weekly'))
@section('page-subtitle', $date->format('F d') . ' – ' . $weekEnd->format('F d, Y'))

@section('topbar-actions')
<div class="d-flex gap-2 no-print">
    <form class="d-flex gap-2" method="GET">
        <input type="week" name="week_start" class="form-control form-control-sm"
               value="{{ $date->format('Y') . '-W' . str_pad($date->weekOfYear, 2, '0', STR_PAD_LEFT) }}">
        <button class="btn btn-sm btn-primary">{{ __('ui.btn_go') }}</button>
    </form>
    <a href="{{ route('reports.export', 'weekly') }}?format=pdf&week_start={{ $date->toDateString() }}" class="btn btn-sm btn-outline-danger"><i class="bi bi-filetype-pdf me-1"></i>PDF</a>
    <a href="{{ route('reports.export', 'weekly') }}?format=excel&week_start={{ $date->toDateString() }}" class="btn btn-sm btn-outline-success"><i class="bi bi-file-earmark-excel me-1"></i>Excel</a>
    <button onclick="window.print()" class="btn btn-sm btn-outline-secondary"><i class="bi bi-printer me-1"></i>{{ __('ui.btn_print') }}</button>
</div>
@endsection

@section('content')

<div class="row g-3 mb-4">
    @php
    $kpis = [
        ['label'=> __('ui.kpi_tasks_completed'), 'value'=> $tasksCompleted,   'icon'=>'bi-check-circle-fill', 'color'=>'#d1fae5','ic'=>'#059669','accent'=>'#10b981'],
        ['label'=> __('ui.kpi_in_progress'),     'value'=> $tasksInProgress,  'icon'=>'bi-arrow-repeat',      'color'=>'#fef3c7','ic'=>'#d97706','accent'=>'#f59e0b'],
        ['label'=> __('ui.kpi_projects_active'), 'value'=> $projectsWorked,   'icon'=>'bi-kanban-fill',       'color'=>'#dbeafe','ic'=>'#2563eb','accent'=>'#3b82f6'],
        ['label'=> __('ui.kpi_companies'),       'value'=> $companiesServed,  'icon'=>'bi-building-fill',     'color'=>'#fce7f3','ic'=>'#db2777','accent'=>'#ec4899'],
        ['label'=> __('ui.kpi_achievements'),    'value'=> $achievementsCount,'icon'=>'bi-trophy-fill',       'color'=>'#fef9c3','ic'=>'#ca8a04','accent'=>'#eab308'],
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

<div class="summary-box mb-4" style="background:#ede9fe;border-left:4px solid #4f46e5">
    <div class="sb-icon" style="background:#4f46e5;color:#fff"><i class="bi bi-stars"></i></div>
    <div>
        <div class="sb-label" style="color:#4f46e5">{{ __('ui.sec_my_weekly_summary') }}</div>
        <p>{{ $summary }}</p>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="table-card">
            <div class="table-card-header">
                <span class="tc-title"><i class="bi bi-list-task me-2" style="color:var(--primary)"></i>{{ __('ui.sec_tasks_this_week') }}</span>
            </div>
            <table class="table mb-0">
                <thead><tr>
                    <th>{{ __('ui.th_task') }}</th>
                    <th>{{ __('ui.th_project') }}</th>
                    <th>{{ __('ui.th_priority') }}</th>
                    <th>{{ __('ui.th_status') }}</th>
                    <th>{{ __('ui.th_due') }}</th>
                </tr></thead>
                <tbody>
                @forelse($taskList as $t)
                <tr>
                    <td class="fw-semibold">{{ $t->title }}</td>
                    <td style="color:#64748b;font-size:.83rem">{{ $t->project?->name ?? '—' }}</td>
                    <td>
                        @php $pa=['high'=>['#fee2e2','#991b1b'],'medium'=>['#fef3c7','#92400e'],'low'=>['#d1fae5','#065f46']] @endphp
                        @if(isset($pa[$t->priority]))
                        <span class="badge" style="background:{{ $pa[$t->priority][0] }};color:{{ $pa[$t->priority][1] }}">{{ __('ui.priority_'.$t->priority) }}</span>
                        @endif
                    </td>
                    <td><span class="badge status-{{ $t->status }}">{{ __('ui.status_'.$t->status) }}</span></td>
                    <td style="color:#64748b;font-size:.82rem">{{ $t->due_date?->format('M d') ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="table-empty"><i class="bi bi-check2-square"></i>{{ __('ui.empty_tasks_week') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="table-card h-100">
            <div class="table-card-header">
                <span class="tc-title"><i class="bi bi-trophy me-2" style="color:#ca8a04"></i>{{ __('ui.sec_achievements_week') }}</span>
            </div>
            <div class="p-3">
                @forelse($achievementList as $a)
                <div class="d-flex align-items-start gap-2 mb-3">
                    <div style="width:36px;height:36px;background:#fef9c3;border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;color:#ca8a04">
                        <i class="bi bi-trophy-fill"></i>
                    </div>
                    <div>
                        <div class="fw-semibold" style="font-size:.85rem">{{ $a->title }}</div>
                        <div style="font-size:.75rem;color:#64748b">{{ ucfirst($a->type) }} · {{ $a->achieved_date->format('M d') }}</div>
                    </div>
                </div>
                @empty
                <div class="table-empty" style="padding:2rem 1rem"><i class="bi bi-trophy"></i>{{ __('ui.empty_achievements_week') }}</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
