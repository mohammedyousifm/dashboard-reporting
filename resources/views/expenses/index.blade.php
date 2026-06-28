@extends('layouts.app')
@section('title', __('ui.pg_expenses'))
@section('page-title', __('ui.pg_expenses'))
@section('page-subtitle', __('ui.pg_expenses_sub'))
@section('topbar-actions')
<a href="{{ route('expenses.create') }}" class="btn btn-sm btn-primary">
    <i class="bi bi-plus-lg me-1"></i>{{ __('ui.btn_add_expense') }}
</a>
@endsection
@section('content')

<!-- Filters -->
<div class="filter-card no-print">
    <form class="row g-2 align-items-end" method="GET">
        <div class="col-md-2">
            <label class="form-label">{{ __('ui.lbl_budget') }}</label>
            <select name="budget_id" class="form-select form-select-sm">
                <option value="">{{ __('ui.ph_all_budgets') }}</option>
                @foreach($budgets as $b)
                    <option value="{{ $b->id }}" {{ request('budget_id')==$b->id?'selected':'' }}>{{ $b->title }} ({{ $b->company->name }})</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">{{ __('ui.lbl_company') }}</label>
            <select name="company_id" class="form-select form-select-sm">
                <option value="">{{ __('ui.ph_all') }}</option>
                @foreach($companies as $c)
                    <option value="{{ $c->id }}" {{ request('company_id')==$c->id?'selected':'' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">{{ __('ui.lbl_project') }}</label>
            <select name="project_id" class="form-select form-select-sm">
                <option value="">{{ __('ui.ph_all') }}</option>
                @foreach($projects as $p)
                    <option value="{{ $p->id }}" {{ request('project_id')==$p->id?'selected':'' }}>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">{{ __('ui.lbl_status') }}</label>
            <select name="status" class="form-select form-select-sm">
                <option value="">{{ __('ui.ph_all') }}</option>
                <option value="paid"       {{ request('status')=='paid'      ?'selected':'' }}>{{ __('ui.status_paid') }}</option>
                <option value="pending"    {{ request('status')=='pending'   ?'selected':'' }}>{{ __('ui.status_pending') }}</option>
                <option value="reimbursed" {{ request('status')=='reimbursed'?'selected':'' }}>{{ __('ui.status_reimbursed') }}</option>
            </select>
        </div>
        <div class="col-md-1">
            <label class="form-label">{{ __('ui.lbl_from') }}</label>
            <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}">
        </div>
        <div class="col-md-1">
            <label class="form-label">{{ __('ui.lbl_to') }}</label>
            <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}">
        </div>
        <div class="col-auto d-flex gap-1">
            <button class="btn btn-sm btn-primary">{{ __('ui.btn_filter') }}</button>
            <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-outline-secondary">{{ __('ui.btn_clear') }}</a>
        </div>
    </form>
</div>

<div class="table-card">
    <div class="table-card-header">
        <span class="tc-title">{{ $expenses->total() }} {{ __('ui.pg_expenses') }}</span>
        <span class="fw-semibold" style="color:#dc2626;font-size:.84rem">SR {{ number_format(\App\Models\Expense::sum('amount'), 2) }} {{ __('ui.kpi_total_spent') }}</span>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr>
                <th>{{ __('ui.th_date') }}</th>
                <th>{{ __('ui.th_item_service') }}</th>
                <th>{{ __('ui.th_vendor') }}</th>
                <th>{{ __('ui.th_budget') }}</th>
                <th>{{ __('ui.th_project') }}</th>
                <th>{{ __('ui.th_method') }}</th>
                <th>{{ __('ui.th_receipt') }}</th>
                <th>{{ __('ui.th_amount') }}</th>
                <th>{{ __('ui.th_status') }}</th>
                <th class="no-print"></th>
            </tr></thead>
            <tbody>
            @forelse($expenses as $exp)
            <tr>
                <td style="font-size:.8rem;color:#64748b;white-space:nowrap">{{ $exp->expense_date->format('M d, Y') }}</td>
                <td><a href="{{ route('expenses.show', $exp) }}" class="fw-semibold text-decoration-none text-dark">{{ $exp->title }}</a></td>
                <td style="font-size:.82rem;color:#64748b">{{ $exp->vendor ?? '—' }}</td>
                <td>
                    @if($exp->budget)
                    <a href="{{ route('budgets.show', $exp->budget) }}" class="text-decoration-none" style="font-size:.82rem">{{ $exp->budget->title }}</a>
                    @else <span style="color:#94a3b8">—</span>
                    @endif
                </td>
                <td style="font-size:.82rem;color:#64748b">{{ $exp->project?->name ?? '—' }}</td>
                <td style="font-size:.82rem">{{ ucwords(str_replace('_',' ',$exp->payment_method)) }}</td>
                <td>
                    @if($exp->invoice_file)
                        <a href="{{ $exp->invoiceUrl() }}" target="_blank" class="btn btn-xs btn-outline-success">
                            <i class="bi bi-{{ $exp->isImage() ? 'image' : 'file-earmark-pdf' }} me-1"></i>{{ __('ui.btn_view') }}
                        </a>
                    @else <span style="color:#94a3b8">—</span>
                    @endif
                </td>
                <td class="fw-semibold" style="color:#dc2626">{{ fmt_money($exp->amount, $exp->currency) }}</td>
                <td>
                    @php $sc=['paid'=>'status-paid','pending'=>'status-pending','reimbursed'=>'status-reimbursed'] @endphp
                    <span class="badge {{ $sc[$exp->status] ?? 'status-inactive' }}">{{ __('ui.status_'.$exp->status) }}</span>
                </td>
                <td class="no-print">
                    <div class="d-flex gap-1 justify-content-end">
                        <a href="{{ route('expenses.edit', $exp) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('expenses.destroy', $exp) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('ui.confirm_delete') }}')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="table-empty">
                    <i class="bi bi-receipt"></i>
                    {{ __('ui.empty_expenses') }} <a href="{{ route('expenses.create') }}">{{ __('ui.add_first_expense') }}</a>
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 border-top">{{ $expenses->links() }}</div>
</div>
@endsection
