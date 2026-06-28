@extends('layouts.app')
@section('title', $company->name)
@section('page-title', $company->name)
@section('topbar-actions')
<a href="{{ route('companies.edit', $company) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil me-1"></i>{{ __('ui.btn_edit') }}</a>
@endsection
@section('content')
<div class="row g-3">
    <div class="col-md-4">
        <div class="chart-card">
            <div class="card-title"><i class="bi bi-building me-2" style="color:#db2777"></i>{{ __('ui.sec_company_info') }}</div>
            <dl class="row mb-0" style="font-size:.84rem">
                <dt class="col-5" style="color:#64748b;font-weight:600">{{ __('ui.lbl_industry') }}</dt><dd class="col-7">{{ $company->industry ?? '—' }}</dd>
                <dt class="col-5" style="color:#64748b;font-weight:600">{{ __('ui.th_status') }}</dt>
                <dd class="col-7"><span class="badge {{ $company->status==='active'?'status-active':'status-inactive' }}">{{ __('ui.status_'.$company->status) }}</span></dd>
                <dt class="col-5" style="color:#64748b;font-weight:600">{{ __('ui.lbl_email') }}</dt><dd class="col-7">{{ $company->contact_email ?? '—' }}</dd>
                <dt class="col-5" style="color:#64748b;font-weight:600">{{ __('ui.lbl_phone') }}</dt><dd class="col-7">{{ $company->contact_phone ?? '—' }}</dd>
                <dt class="col-5" style="color:#64748b;font-weight:600">{{ __('ui.lbl_address') }}</dt><dd class="col-7">{{ $company->address ?? '—' }}</dd>
            </dl>
        </div>
    </div>
    <div class="col-md-8">
        <div class="table-card">
            <div class="table-card-header">
                <span class="tc-title">{{ __('ui.sec_projects') }} ({{ $company->projects->count() }})</span>
                <a href="{{ route('projects.create') }}" class="btn btn-xs btn-outline-primary"><i class="bi bi-plus-lg me-1"></i>{{ __('ui.btn_add_project') }}</a>
            </div>
            <table class="table table-sm mb-0">
                <thead><tr>
                    <th>{{ __('ui.th_project') }}</th>
                    <th>{{ __('ui.th_status') }}</th>
                    <th>{{ __('ui.th_tasks') }}</th>
                </tr></thead>
                <tbody>
                @forelse($company->projects as $p)
                <tr>
                    <td class="fw-semibold"><a href="{{ route('projects.show', $p) }}" class="text-decoration-none text-dark">{{ $p->name }}</a></td>
                    <td><span class="badge status-{{ $p->status }}">{{ __('ui.status_'.$p->status) }}</span></td>
                    <td>{{ $p->tasks->count() }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="table-empty"><i class="bi bi-kanban"></i>{{ __('ui.empty_projects') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
