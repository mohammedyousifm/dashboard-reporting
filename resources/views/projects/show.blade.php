@extends('layouts.app')
@section('title', $project->name)
@section('page-title', $project->name)
@section('topbar-actions')
<a href="{{ route('projects.edit', $project) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil me-1"></i>{{ __('ui.btn_edit') }}</a>
@endsection
@section('content')
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="kpi-card" style="--kpi-accent:#3b82f6">
            <div class="kpi-icon" style="background:#dbeafe;color:#2563eb"><i class="bi bi-list-task"></i></div>
            <div class="kpi-value">{{ $project->tasks->count() }}</div>
            <div class="kpi-label">{{ __('ui.kpi_tasks') }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="kpi-card" style="--kpi-accent:#10b981">
            <div class="kpi-icon" style="background:#d1fae5;color:#059669"><i class="bi bi-check-circle-fill"></i></div>
            <div class="kpi-value">{{ $project->tasks->where('status','completed')->count() }}</div>
            <div class="kpi-label">{{ __('ui.kpi_completed_tasks') }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="kpi-card" style="--kpi-accent:#eab308">
            <div class="kpi-icon" style="background:#fef9c3;color:#ca8a04"><i class="bi bi-trophy-fill"></i></div>
            <div class="kpi-value">{{ $project->achievements->count() }}</div>
            <div class="kpi-label">{{ __('ui.kpi_achievements') }}</div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="chart-card">
            <div class="card-title">{{ __('ui.sec_project_details') }}</div>
            <dl class="row small">
                <dt class="col-5 text-muted">{{ __('ui.lbl_company') }}</dt><dd class="col-7">{{ $project->company?->name }}</dd>
                <dt class="col-5 text-muted">{{ __('ui.lbl_category') }}</dt><dd class="col-7">{{ $project->category?->name ?? '—' }}</dd>
                <dt class="col-5 text-muted">{{ __('ui.lbl_status') }}</dt><dd class="col-7"><span class="badge status-{{ $project->status }}">{{ __('ui.status_'.$project->status) }}</span></dd>
                <dt class="col-5 text-muted">{{ __('ui.lbl_start_date') }}</dt><dd class="col-7">{{ $project->start_date?->format('M d, Y') ?? '—' }}</dd>
                <dt class="col-5 text-muted">{{ __('ui.lbl_end_date') }}</dt><dd class="col-7">{{ $project->end_date?->format('M d, Y') ?? '—' }}</dd>
            </dl>
            @if($project->description)
            <p class="text-muted small mt-2">{{ $project->description }}</p>
            @endif
        </div>
    </div>
    <div class="col-md-8">
        <div class="table-card">
            <div class="table-card-header">
                <span class="tc-title">{{ __('ui.nav_tasks') }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead><tr>
                        <th>{{ __('ui.th_task') }}</th>
                        <th>{{ __('ui.th_status') }}</th>
                        <th>{{ __('ui.th_priority') }}</th>
                        <th>{{ __('ui.th_due') }}</th>
                    </tr></thead>
                    <tbody>
                    @forelse($project->tasks as $t)
                    <tr>
                        <td class="fw-medium">{{ $t->title }}</td>
                        <td><span class="badge status-{{ $t->status }}">{{ __('ui.status_'.$t->status) }}</span></td>
                        <td class="text-muted small">{{ __('ui.priority_'.$t->priority) }}</td>
                        <td class="text-muted small">{{ $t->due_date?->format('M d') ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="table-empty"><i class="bi bi-check2-square"></i>{{ __('ui.empty_tasks') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
