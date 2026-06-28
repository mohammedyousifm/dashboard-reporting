@extends('layouts.app')
@section('title', $achievement->title)
@section('page-title', $achievement->title)
@section('topbar-actions')
<a href="{{ route('achievements.edit', $achievement) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil me-1"></i>{{ __('ui.btn_edit') }}</a>
@endsection
@section('content')
<div class="chart-card" style="max-width:600px">
    <div class="d-flex align-items-center gap-3 mb-4">
        <div style="width:56px;height:56px;background:#fef9c3;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.6rem;color:#ca8a04">
            <i class="bi bi-trophy-fill"></i>
        </div>
        <div>
            <h5 class="mb-1 fw-bold">{{ $achievement->title }}</h5>
            <span class="badge status-active">{{ ucfirst($achievement->type) }}</span>
        </div>
    </div>
    <dl class="row mb-0" style="font-size:.88rem">
        <dt class="col-4" style="color:#64748b;font-weight:600">{{ __('ui.lbl_project') }}</dt>
        <dd class="col-8">{{ $achievement->project?->name ?? '—' }}</dd>
        <dt class="col-4" style="color:#64748b;font-weight:600">{{ __('ui.lbl_achieved_date') }}</dt>
        <dd class="col-8">{{ $achievement->achieved_date->format('F d, Y') }}</dd>
        @if($achievement->description)
        <dt class="col-4" style="color:#64748b;font-weight:600">{{ __('ui.lbl_description') }}</dt>
        <dd class="col-8">{{ $achievement->description }}</dd>
        @endif
    </dl>
</div>
@endsection
