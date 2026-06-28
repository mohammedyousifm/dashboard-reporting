@extends('layouts.app')
@section('title', 'Record Budget')
@section('page-title', 'Record Budget Received')
@section('page-subtitle', 'Money given to you by the company')

@section('content')
<div class="row g-4">
    <div class="col-lg-7">
        <div class="chart-card">
            <form action="{{ route('budgets.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Budget Title / Purpose *</label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title') }}" placeholder="e.g. Q2 Developer Tools Budget" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Company (Who gave you this?) *</label>
                        <select name="company_id" class="form-select @error('company_id') is-invalid @enderror" required>
                            <option value="">Select company…</option>
                            @foreach($companies as $c)
                                <option value="{{ $c->id }}" {{ old('company_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                        @error('company_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Linked Project (optional)</label>
                        <select name="project_id" class="form-select">
                            <option value="">Not linked to a specific project</option>
                            @foreach($projects as $p)
                                <option value="{{ $p->id }}" {{ old('project_id') == $p->id ? 'selected' : '' }}>{{ $p->name }} — {{ $p->company?->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-5">
                        <label class="form-label fw-semibold">Amount Received *</label>
                        <div class="input-group">
                            <span class="input-group-text">SR</span>
                            <input type="number" name="amount" step="0.01" min="0.01"
                                   class="form-control @error('amount') is-invalid @enderror"
                                   value="{{ old('amount') }}" placeholder="1000.00" required>
                            @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Currency</label>
                        <select name="currency" class="form-select">
                            @foreach(['SAR','USD','EUR','GBP','AED','EGP','OMR','KWD','QAR','BHD'] as $cur)
                            <option value="{{ $cur }}" {{ old('currency','SAR') == $cur ? 'selected' : '' }}>{{ $cur }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Date Received *</label>
                        <input type="date" name="received_date"
                               class="form-control @error('received_date') is-invalid @enderror"
                               value="{{ old('received_date', now()->toDateString()) }}" required>
                        @error('received_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status','active')=='active'?'selected':'' }}>Active (still spending)</option>
                            <option value="closed" {{ old('status')=='closed'?'selected':'' }}>Closed (fully accounted)</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" class="form-control" rows="3"
                                  placeholder="Any notes from accounting, purpose details…">{{ old('notes') }}</textarea>
                    </div>

                    <div class="col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Save Budget
                        </button>
                        <a href="{{ route('budgets.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Helper card --}}
    <div class="col-lg-5">
        <div class="chart-card" style="border-left:4px solid #4f46e5">
            <div class="d-flex align-items-center gap-2 mb-3">
                <i class="bi bi-info-circle-fill text-primary fs-5"></i>
                <span class="fw-semibold">How Budgets Work</span>
            </div>
            <ol class="small text-muted ps-3" style="line-height:2">
                <li>Accounting gives you money → <strong>Record a Budget</strong></li>
                <li>You spend on tools, services, subscriptions → <strong>Add Expenses</strong></li>
                <li>Each expense links to the budget, project & task</li>
                <li>The system tracks your <strong>remaining balance</strong> automatically</li>
                <li>Generate a <strong>spending report</strong> to show accounting exactly where each dollar went</li>
            </ol>
        </div>
    </div>
</div>
@endsection
