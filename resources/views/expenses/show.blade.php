@extends('layouts.app')
@section('title', $expense->title)
@section('page-title', $expense->title)
@section('topbar-actions')
<div class="d-flex gap-2 no-print">
    <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
    <button onclick="window.print()" class="btn btn-sm btn-outline-secondary"><i class="bi bi-printer me-1"></i>Print Receipt</button>
</div>
@endsection

@section('content')
<div class="row g-3">
    <div class="col-md-6">
        <div class="chart-card">
            <div class="card-title">{{ __('ui.sec_expense_details') }}</div>
            <dl class="row">
                <dt class="col-5 text-muted small">{{ __('ui.th_date') }}</dt>
                <dd class="col-7">{{ $expense->expense_date->format('F d, Y') }}</dd>

                <dt class="col-5 text-muted small">{{ __('ui.th_vendor') }}</dt>
                <dd class="col-7">{{ $expense->vendor ?? '—' }}</dd>

                <dt class="col-5 text-muted small">{{ __('ui.th_amount') }}</dt>
                <dd class="col-7 fw-bold text-danger fs-5">{{ fmt_money($expense->amount, $expense->currency) }}</dd>

                <dt class="col-5 text-muted small">{{ __('ui.lbl_payment_method') }}</dt>
                <dd class="col-7">{{ ucwords(str_replace('_',' ',$expense->payment_method)) }}</dd>

                <dt class="col-5 text-muted small">{{ __('ui.th_status') }}</dt>
                <dd class="col-7">
                    <span class="badge status-{{ $expense->status }}">{{ __('ui.status_'.$expense->status) }}</span>
                </dd>
            </dl>
        </div>
    </div>

    <div class="col-md-6">
        <div class="chart-card">
            <div class="card-title">{{ __('ui.sec_linked_to') }}</div>
            <dl class="row">
                <dt class="col-5 text-muted small">{{ __('ui.lbl_budget') }}</dt>
                <dd class="col-7">
                    @if($expense->budget)
                        <a href="{{ route('budgets.show', $expense->budget) }}" class="text-decoration-none">{{ $expense->budget->title }}</a>
                    @elseif($expense->parent_expense_id)
                        <span class="badge" style="background:#fff7ed;color:#9a3412;border:1px solid #fed7aa">Unbudgeted overflow</span>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </dd>

                <dt class="col-5 text-muted small">{{ __('ui.lbl_company') }}</dt>
                <dd class="col-7">{{ $expense->company->name }}</dd>

                <dt class="col-5 text-muted small">{{ __('ui.lbl_project') }}</dt>
                <dd class="col-7">{{ $expense->project?->name ?? '—' }}</dd>

                <dt class="col-5 text-muted small">{{ __('ui.lbl_task') }}</dt>
                <dd class="col-7">{{ $expense->task?->title ?? '—' }}</dd>
            </dl>
        </div>
    </div>

    {{-- Budget Split info --}}
    @if($expense->parentExpense || $expense->overflowExpense)
    <div class="col-12">
        <div class="chart-card" style="border-left:4px solid #ea580c">
            <div class="d-flex align-items-center gap-2 mb-3">
                <i class="bi bi-split" style="color:#ea580c"></i>
                <span class="fw-semibold">Split Expense</span>
                <span class="badge" style="background:#fff7ed;color:#9a3412;border:1px solid #fed7aa;font-size:.72rem">This expense was funded across two budgets</span>
            </div>
            <div class="row g-2">
                @if($expense->parentExpense)
                {{-- This is the overflow record --}}
                <div class="col-md-6">
                    <div class="p-3 rounded-3" style="background:#f0fdf4;border:1px solid #bbf7d0">
                        <div class="small text-muted mb-1">Primary portion</div>
                        <div class="fw-bold">{{ fmt_money($expense->parentExpense->amount, $expense->parentExpense->currency) }}</div>
                        <div class="small text-muted">from <a href="{{ route('budgets.show', $expense->parentExpense->budget) }}">{{ $expense->parentExpense->budget->title }}</a></div>
                        <div class="mt-1"><a href="{{ route('expenses.show', $expense->parentExpense) }}" class="btn btn-xs btn-outline-success btn-sm" style="font-size:.7rem;padding:2px 8px">View primary record</a></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 rounded-3" style="background:#fff7ed;border:1px solid #fed7aa">
                        <div class="small text-muted mb-1">Overflow portion (this record)</div>
                        <div class="fw-bold" style="color:#9a3412">{{ fmt_money($expense->amount, $expense->currency) }}</div>
                        <div class="small text-muted">from <a href="{{ route('budgets.show', $expense->budget) }}">{{ $expense->budget->title }}</a></div>
                    </div>
                </div>
                @else
                {{-- This is the primary record --}}
                <div class="col-md-6">
                    <div class="p-3 rounded-3" style="background:#f0fdf4;border:1px solid #bbf7d0">
                        <div class="small text-muted mb-1">Primary portion (this record)</div>
                        <div class="fw-bold" style="color:#166534">{{ fmt_money($expense->amount, $expense->currency) }}</div>
                        <div class="small text-muted">from <a href="{{ route('budgets.show', $expense->budget) }}">{{ $expense->budget->title }}</a></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 rounded-3" style="background:#fff7ed;border:1px solid #fed7aa">
                        <div class="small text-muted mb-1">Overflow portion</div>
                        <div class="fw-bold" style="color:#9a3412">{{ fmt_money($expense->overflowExpense->amount, $expense->overflowExpense->currency) }}</div>
                        @if($expense->overflowExpense->budget)
                            <div class="small text-muted">from <a href="{{ route('budgets.show', $expense->overflowExpense->budget) }}">{{ $expense->overflowExpense->budget->title }}</a></div>
                        @else
                            <div class="small"><span class="badge" style="background:#fff7ed;color:#9a3412;border:1px solid #fed7aa">Unbudgeted — no secondary budget</span></div>
                        @endif
                        <div class="mt-1"><a href="{{ route('expenses.show', $expense->overflowExpense) }}" class="btn btn-sm btn-outline-warning" style="font-size:.7rem;padding:2px 8px">View overflow record</a></div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="small text-muted">Total expense: <strong>{{ fmt_money($expense->amount + $expense->overflowExpense->amount, $expense->currency) }}</strong></div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- Invoice / Receipt File --}}
    <div class="col-12">
        <div class="chart-card">
            <div class="card-title mb-3">{{ __('ui.sec_invoice_receipt') }}</div>
            @if($expense->invoice_file)
                @if($expense->isImage())
                    <div class="mb-3">
                        <img src="{{ $expense->invoiceUrl() }}" alt="Invoice"
                             style="max-width:100%;max-height:480px;border-radius:10px;border:1px solid #e5e7eb;object-fit:contain">
                    </div>
                @else
                    <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:#fef2f2;border:1px solid #fecaca">
                        <i class="bi bi-file-earmark-pdf-fill text-danger fs-1"></i>
                        <div>
                            <div class="fw-semibold">{{ $expense->invoice_file_name }}</div>
                            <div class="small text-muted">PDF Document</div>
                        </div>
                    </div>
                @endif
                <div class="mt-3 d-flex gap-2">
                    <a href="{{ $expense->invoiceUrl() }}" target="_blank" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-eye me-1"></i>{{ __('ui.btn_view_file') }}
                    </a>
                    <a href="{{ $expense->invoiceUrl() }}" download="{{ $expense->invoice_file_name }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-download me-1"></i>{{ __('ui.btn_download') }}
                    </a>
                </div>
            @else
                <div class="text-center text-muted py-4">
                    <i class="bi bi-file-earmark-x d-block fs-1 mb-2 opacity-25"></i>
                    {{ __('ui.empty_no_invoice') }}
                    <div class="mt-2">
                        <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-sm btn-outline-primary">{{ __('ui.btn_upload_invoice') }}</a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if($expense->notes)
    <div class="col-12">
        <div class="chart-card">
            <div class="fw-semibold small text-muted mb-1">Notes</div>
            <p class="mb-0">{{ $expense->notes }}</p>
        </div>
    </div>
    @endif
</div>
@endsection
