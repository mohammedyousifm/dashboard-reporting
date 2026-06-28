@extends('layouts.app')
@section('title', $task->title)
@section('page-title', $task->title)
@section('topbar-actions')
<a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil me-1"></i>{{ __('ui.btn_edit') }}</a>
@endsection
@section('content')
<div class="chart-card" style="max-width:600px">
    <dl class="row">
        <dt class="col-4 text-muted">{{ __('ui.lbl_project') }}</dt><dd class="col-8">{{ $task->project?->name }}</dd>
        <dt class="col-4 text-muted">{{ __('ui.lbl_company') }}</dt><dd class="col-8">{{ $task->project?->company?->name ?? '—' }}</dd>
        <dt class="col-4 text-muted">{{ __('ui.lbl_category') }}</dt><dd class="col-8">{{ $task->category?->name ?? '—' }}</dd>
        <dt class="col-4 text-muted">{{ __('ui.lbl_priority') }}</dt><dd class="col-8">{{ __('ui.priority_'.$task->priority) }}</dd>
        <dt class="col-4 text-muted">{{ __('ui.lbl_status') }}</dt><dd class="col-8"><span class="badge status-{{ $task->status }}">{{ __('ui.status_'.$task->status) }}</span></dd>
        <dt class="col-4 text-muted">{{ __('ui.lbl_due_date') }}</dt><dd class="col-8">{{ $task->due_date?->format('F d, Y') ?? '—' }}</dd>
        <dt class="col-4 text-muted">{{ __('ui.lbl_completed_date') }}</dt><dd class="col-8">{{ $task->completed_date?->format('F d, Y') ?? '—' }}</dd>
        <dt class="col-4 text-muted">{{ __('ui.lbl_description') }}</dt><dd class="col-8">{{ $task->description ?? '—' }}</dd>
    </dl>
</div>
@endsection
