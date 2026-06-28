@extends('layouts.app')
@section('title', __('ui.pg_budgets'))
@section('page-title', __('ui.pg_budgets'))
@section('page-subtitle', __('ui.pg_budgets_sub'))
@section('topbar-actions')
<a href="{{ route('budgets.create') }}" class="btn btn-sm btn-primary">
    <i class="bi bi-plus-lg me-1"></i>{{ __('ui.btn_record_budget') }}
</a>
@endsection
@section('content')

<!-- KPIs -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="kpi-card" style="--kpi-accent:#10b981">
            <div class="kpi-icon" style="background:#d1fae5;color:#059669"><i class="bi bi-arrow-down-circle-fill"></i></div>
            <div class="kpi-value" style="font-size:1.5rem">SR {{ number_format($totalReceived, 2) }}</div>
            <div class="kpi-label">{{ __('ui.kpi_total_received') }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="kpi-card" style="--kpi-accent:#ef4444">
            <div class="kpi-icon" style="background:#fee2e2;color:#dc2626"><i class="bi bi-arrow-up-circle-fill"></i></div>
            <div class="kpi-value" style="font-size:1.5rem">SR {{ number_format($totalSpent, 2) }}</div>
            <div class="kpi-label">{{ __('ui.kpi_total_spent') }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="kpi-card" style="--kpi-accent:#7c3aed">
            <div class="kpi-icon" style="background:#ede9fe;color:#7c3aed"><i class="bi bi-piggy-bank-fill"></i></div>
            <div class="kpi-value" style="font-size:1.5rem;color:{{ $totalRemaining >= 0 ? '#059669' : '#dc2626' }}">SR {{ number_format($totalRemaining, 2) }}</div>
            <div class="kpi-label">{{ __('ui.kpi_total_remaining') }}</div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="filter-card no-print">
    <form class="row g-2 align-items-end" method="GET">
        <div class="col-md-4">
            <label class="form-label">{{ __('ui.lbl_company') }}</label>
            <select name="company_id" class="form-select form-select-sm">
                <option value="">{{ __('ui.ph_all_companies') }}</option>
                @foreach($companies as $c)
                    <option value="{{ $c->id }}" {{ request('company_id')==$c->id?'selected':'' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('ui.lbl_status') }}</label>
            <select name="status" class="form-select form-select-sm">
                <option value="">{{ __('ui.ph_all_statuses') }}</option>
                <option value="active" {{ request('status')=='active'?'selected':'' }}>{{ __('ui.status_active') }}</option>
                <option value="closed" {{ request('status')=='closed'?'selected':'' }}>{{ __('ui.status_closed') }}</option>
            </select>
        </div>
        <div class="col-auto d-flex gap-1">
            <button class="btn btn-sm btn-primary">{{ __('ui.btn_filter') }}</button>
            <a href="{{ route('budgets.index') }}" class="btn btn-sm btn-outline-secondary">{{ __('ui.btn_clear') }}</a>
        </div>
    </form>
</div>

<!-- Table -->
<div class="table-card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr>
                <th>{{ __('ui.sec_budget_purpose') }}</th>
                <th>{{ __('ui.th_company') }}</th>
                <th>{{ __('ui.th_project') }}</th>
                <th>{{ __('ui.th_received') }}</th>
                <th>{{ __('ui.th_spent') }}</th>
                <th>{{ __('ui.th_remaining') }}</th>
                <th>{{ __('ui.th_usage') }}</th>
                <th>{{ __('ui.th_status') }}</th>
                <th></th>
            </tr></thead>
            <tbody>
            @forelse($budgets as $budget)
            @php
                $spent     = $budget->totalSpent();
                $remaining = $budget->remaining();
                $pct       = $budget->usedPercent();
                $barColor  = $pct >= 90 ? '#dc2626' : ($pct >= 70 ? '#d97706' : '#059669');
            @endphp
            <tr>
                <td>
                    <a href="{{ route('budgets.show', $budget) }}" class="fw-semibold text-decoration-none text-dark">{{ $budget->title }}</a>
                    <div style="font-size:.76rem;color:#64748b">{{ $budget->received_date->format('M d, Y') }} · {{ $budget->expenses_count }} {{ __('ui.expenses_count') }}</div>
                </td>
                <td style="font-size:.83rem;color:#64748b">{{ $budget->company->name }}</td>
                <td style="font-size:.83rem;color:#64748b">{{ $budget->project?->name ?? '—' }}</td>
                <td class="fw-semibold" style="color:#059669">{{ fmt_money($budget->amount, $budget->currency) }}</td>
                <td class="fw-semibold" style="color:#dc2626">{{ fmt_money($spent, $budget->currency) }}</td>
                <td class="fw-semibold" style="color:{{ $remaining >= 0 ? '#059669' : '#dc2626' }}">{{ fmt_money($remaining, $budget->currency) }}</td>
                <td style="min-width:130px">
                    <div class="d-flex align-items-center gap-2">
                        <div class="progress flex-grow-1" style="height:6px;border-radius:4px">
                            <div class="progress-bar" style="width:{{ min($pct,100) }}%;background:{{ $barColor }};border-radius:4px"></div>
                        </div>
                        <span style="font-size:.75rem;color:#64748b;white-space:nowrap">{{ $pct }}%</span>
                    </div>
                </td>
                <td><span class="badge {{ $budget->status === 'active' ? 'status-active' : 'status-inactive' }}">{{ __('ui.status_'.$budget->status) }}</span></td>
                <td>
                    <div class="d-flex gap-1 justify-content-end flex-nowrap">
                        <a href="{{ route('expenses.create', ['budget_id' => $budget->id]) }}" class="btn btn-sm btn-outline-primary" title="{{ __('ui.btn_add_expense') }}">
                            <i class="bi bi-plus-lg"></i>
                        </a>
                        <a href="{{ route('budgets.edit', $budget) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('budgets.destroy', $budget) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('ui.confirm_delete_budget') }}')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="table-empty">
                    <i class="bi bi-wallet2"></i>
                    {{ __('ui.empty_budgets') }} <a href="{{ route('budgets.create') }}">{{ __('ui.record_first_budget') }}</a>
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 border-top">{{ $budgets->links() }}</div>
</div>
@endsection
