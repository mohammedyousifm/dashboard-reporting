@extends('layouts.app')
@section('title', $budget->title)
@section('page-title', $budget->title)
@section('page-subtitle', $budget->company->name . ' · Received ' . $budget->received_date->format('M d, Y'))

@section('topbar-actions')
<div class="d-flex gap-2 no-print">
    <a href="{{ route('expenses.create', ['budget_id' => $budget->id]) }}" class="btn btn-sm btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Add Expense
    </a>
    <a href="{{ route('budgets.edit', $budget) }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-pencil me-1"></i>Edit
    </a>
    <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-printer me-1"></i>Print
    </button>
</div>
@endsection

@section('content')

@php
    $cur       = $budget->currency;
    $spent     = $budget->totalSpent();
    $remaining = $budget->remaining();
    $pct       = $budget->usedPercent();
    $barColor  = $pct >= 90 ? '#dc2626' : ($pct >= 70 ? '#d97706' : '#059669');
@endphp

{{-- KPIs --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="kpi-card" style="--kpi-accent:#059669">
            <div class="kpi-icon" style="background:#d1fae5;color:#059669"><i class="bi bi-arrow-down-circle-fill"></i></div>
            <div class="kpi-value text-success" style="font-size:1.5rem">{{ fmt_money($budget->amount, $cur) }}</div>
            <div class="kpi-label">{{ __('ui.kpi_budget_received') }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card" style="--kpi-accent:#dc2626">
            <div class="kpi-icon" style="background:#fee2e2;color:#dc2626"><i class="bi bi-receipt"></i></div>
            <div class="kpi-value text-danger" style="font-size:1.5rem">{{ fmt_money($spent, $cur) }}</div>
            <div class="kpi-label">{{ __('ui.kpi_total_spent') }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card" style="--kpi-accent:#7c3aed">
            <div class="kpi-icon" style="background:#ede9fe;color:#7c3aed"><i class="bi bi-piggy-bank-fill"></i></div>
            <div class="kpi-value" style="color:{{ $remaining >= 0 ? '#059669' : '#dc2626' }};font-size:1.5rem">
                {{ fmt_money($remaining, $cur) }}
            </div>
            <div class="kpi-label">{{ __('ui.kpi_remaining') }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card" style="--kpi-accent:#d97706">
            <div class="kpi-icon" style="background:#fef3c7;color:#d97706"><i class="bi bi-percent"></i></div>
            <div class="kpi-value" style="color:{{ $barColor }}">{{ $pct }}%</div>
            <div class="kpi-label">{{ __('ui.kpi_budget_used') }}</div>
        </div>
    </div>
</div>

{{-- Progress bar --}}
<div class="chart-card mb-4">
    <div class="d-flex justify-content-between small mb-2">
        <span class="fw-semibold">{{ __('ui.sec_budget_usage') }}</span>
        <span>{{ fmt_money($spent, $cur) }} of {{ fmt_money($budget->amount, $cur) }}</span>
    </div>
    <div class="progress" style="height:14px;border-radius:8px">
        <div class="progress-bar" style="width:{{ min($pct,100) }}%;background:{{ $barColor }};border-radius:8px;transition:width .5s">
        </div>
    </div>
    @if($remaining < 0)
    <div class="text-danger small mt-2"><i class="bi bi-exclamation-triangle me-1"></i>Over budget by {{ fmt_money(abs($remaining), $cur) }}</div>
    @else
    <div class="text-muted small mt-2">{{ fmt_money($remaining, $cur) }} remaining</div>
    @endif
</div>

<div class="row g-3 mb-4">
    {{-- Spending by Project --}}
    @if($byProject->count())
    <div class="col-md-6">
        <div class="chart-card h-100">
            <div class="card-title">{{ __('ui.sec_spending_by_project') }}</div>
            @foreach($byProject as $row)
            @php $bpct = $spent > 0 ? round($row->total / $spent * 100, 1) : 0 @endphp
            <div class="mb-3">
                <div class="d-flex justify-content-between small mb-1">
                    <span>{{ $row->name }}</span>
                    <span class="fw-semibold">{{ fmt_money($row->total, $cur) }} <span class="text-muted">({{ $bpct }}%)</span></span>
                </div>
                <div class="progress" style="height:6px">
                    <div class="progress-bar bg-primary" style="width:{{ $bpct }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Spending by Payment Method --}}
    <div class="{{ $byProject->count() ? 'col-md-6' : 'col-md-12' }}">
        <div class="chart-card h-100">
            <div class="card-title">{{ __('ui.sec_payment_methods') }}</div>
            @forelse($byPaymentMethod as $row)
            @php $mpct = $spent > 0 ? round($row->total / $spent * 100, 1) : 0 @endphp
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <div class="fw-medium small">{{ ucwords(str_replace('_',' ',$row->payment_method)) }}</div>
                    <div class="text-muted" style="font-size:.7rem">{{ $row->count }} expense(s)</div>
                </div>
                <div class="text-end">
                    <div class="fw-semibold">{{ fmt_money($row->total, $cur) }}</div>
                    <div class="text-muted small">{{ $mpct }}%</div>
                </div>
            </div>
            @empty
            <p class="text-muted small">No expenses yet.</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Expenses Table --}}
<div class="table-card">
    <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
        <span class="fw-semibold small">All Expenses ({{ $budget->expenses->count() }})</span>
        <a href="{{ route('expenses.create', ['budget_id' => $budget->id]) }}" class="btn btn-sm btn-primary no-print">
            <i class="bi bi-plus-lg me-1"></i>Add Expense
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>{{ __('ui.th_date') }}</th>
                    <th>{{ __('ui.th_item_service') }}</th>
                    <th>{{ __('ui.th_vendor') }}</th>
                    <th>{{ __('ui.th_project') }}</th>
                    <th>{{ __('ui.th_task') }}</th>
                    <th>{{ __('ui.th_method') }}</th>
                    <th>{{ __('ui.th_receipt') }}</th>
                    <th>{{ __('ui.th_amount') }}</th>
                    <th>{{ __('ui.th_status') }}</th>
                    <th class="no-print"></th>
                </tr>
            </thead>
            <tbody>
            @forelse($budget->expenses as $exp)
            <tr>
                <td class="text-muted small">{{ $exp->expense_date->format('M d, Y') }}</td>
                <td class="fw-medium">{{ $exp->title }}</td>
                <td class="text-muted small">{{ $exp->vendor ?? '—' }}</td>
                <td class="text-muted small">{{ $exp->project?->name ?? '—' }}</td>
                <td class="text-muted small">{{ $exp->task?->title ?? '—' }}</td>
                <td class="small">{{ ucwords(str_replace('_',' ',$exp->payment_method)) }}</td>
                <td>
                    @if($exp->invoice_file)
                        <a href="{{ $exp->invoiceUrl() }}" target="_blank"
                           class="btn btn-sm btn-outline-success" title="{{ $exp->invoice_file_name }}">
                            <i class="bi bi-{{ $exp->isImage() ? 'image' : 'file-earmark-pdf' }} me-1"></i>View
                        </a>
                    @else
                        <span class="text-muted small">—</span>
                    @endif
                </td>
                <td class="fw-semibold text-danger">{{ fmt_money($exp->amount, $exp->currency) }}</td>
                <td>
                    <span class="badge status-{{ $exp->status }}">{{ __('ui.status_'.$exp->status) }}</span>
                </td>
                <td class="no-print">
                    <a href="{{ route('expenses.edit', $exp) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                    <form action="{{ route('expenses.destroy', $exp) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="10" class="text-center text-muted py-4">No expenses yet. <a href="{{ route('expenses.create', ['budget_id'=>$budget->id]) }}">Add one</a></td></tr>
            @endforelse
            </tbody>
            @if($budget->expenses->count())
            <tfoot>
                <tr class="table-light fw-bold">
                    <td colspan="7" class="text-end">{{ __('ui.th_spent') }}:</td>
                    <td class="text-danger">{{ fmt_money($spent, $cur) }}</td>
                    <td colspan="2"></td>
                </tr>
                <tr class="fw-bold">
                    <td colspan="7" class="text-end">{{ __('ui.th_remaining') }}:</td>
                    <td style="color:{{ $remaining >= 0 ? '#059669' : '#dc2626' }}">{{ fmt_money($remaining, $cur) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

@if($budget->notes)
<div class="chart-card mt-3">
    <div class="fw-semibold small text-muted mb-1">{{ __('ui.lbl_accounting_notes') }}</div>
    <p class="mb-0">{{ $budget->notes }}</p>
</div>
@endif
@endsection
