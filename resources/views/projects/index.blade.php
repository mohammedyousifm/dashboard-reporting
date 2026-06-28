@extends('layouts.app')
@section('title', __('ui.pg_projects'))
@section('page-title', __('ui.pg_projects'))
@section('topbar-actions')
<a href="{{ route('projects.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg me-1"></i>{{ __('ui.btn_add_project') }}</a>
@endsection
@section('content')

<div class="filter-card no-print">
    <form class="row g-2 align-items-end" method="GET">
        <div class="col-md-4">
            <label class="form-label">{{ __('ui.lbl_company') }}</label>
            <select name="company_id" class="form-select form-select-sm">
                <option value="">{{ __('ui.ph_all_companies') }}</option>
                @foreach($companies as $c)
                    <option value="{{ $c->id }}" {{ request('company_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('ui.lbl_status') }}</label>
            <select name="status" class="form-select form-select-sm">
                <option value="">{{ __('ui.ph_all_statuses') }}</option>
                <option value="active"    {{ request('status')=='active'   ?'selected':'' }}>{{ __('ui.status_active') }}</option>
                <option value="completed" {{ request('status')=='completed'?'selected':'' }}>{{ __('ui.status_completed') }}</option>
                <option value="on_hold"   {{ request('status')=='on_hold'  ?'selected':'' }}>{{ __('ui.status_on_hold') }}</option>
            </select>
        </div>
        <div class="col-auto d-flex gap-1">
            <button class="btn btn-sm btn-primary">{{ __('ui.btn_filter') }}</button>
            <a href="{{ route('projects.index') }}" class="btn btn-sm btn-outline-secondary">{{ __('ui.btn_clear') }}</a>
        </div>
    </form>
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr>
                <th>{{ __('ui.th_project') }}</th>
                <th>{{ __('ui.th_company') }}</th>
                <th>{{ __('ui.th_category') }}</th>
                <th>{{ __('ui.th_status') }}</th>
                <th>{{ __('ui.th_tasks') }}</th>
                <th>{{ __('ui.th_dates') }}</th>
                <th></th>
            </tr></thead>
            <tbody>
            @forelse($projects as $p)
            <tr>
                <td class="fw-semibold"><a href="{{ route('projects.show', $p) }}" class="text-decoration-none text-dark">{{ $p->name }}</a></td>
                <td style="color:#64748b;font-size:.83rem">{{ $p->company?->name ?? '—' }}</td>
                <td>
                    @if($p->category)
                    <span class="badge" style="background:{{ $p->category->color }}20;color:{{ $p->category->color }}">{{ $p->category->name }}</span>
                    @else <span style="color:#94a3b8">—</span>
                    @endif
                </td>
                <td><span class="badge status-{{ $p->status }}">{{ __('ui.status_'.$p->status) }}</span></td>
                <td><span class="badge bg-warning bg-opacity-10 text-warning">{{ $p->tasks_count }}</span></td>
                <td style="color:#64748b;font-size:.8rem">
                    {{ $p->start_date?->format('M d, Y') ?? '—' }}
                    @if($p->end_date) → {{ $p->end_date->format('M d, Y') }} @endif
                </td>
                <td>
                    <div class="d-flex gap-1 justify-content-end">
                        <a href="{{ route('projects.edit', $p) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('projects.destroy', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('ui.confirm_delete') }}')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="table-empty"><i class="bi bi-kanban"></i>{{ __('ui.empty_projects') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 border-top">{{ $projects->links() }}</div>
</div>
@endsection
