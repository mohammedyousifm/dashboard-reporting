@extends('layouts.app')
@section('title', __('ui.pg_edit_budget'))
@section('page-title', __('ui.pg_edit_budget'))

@section('content')
<div class="chart-card" style="max-width:680px">
    <form action="{{ route('budgets.update', $budget) }}" method="POST">
        @csrf @method('PUT')
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label fw-semibold">{{ __('ui.lbl_title') }} *</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $budget->title) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">{{ __('ui.lbl_company') }} *</label>
                <select name="company_id" class="form-select" required>
                    @foreach($companies as $c)
                        <option value="{{ $c->id }}" {{ old('company_id',$budget->company_id)==$c->id?'selected':'' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">{{ __('ui.lbl_linked_project') }}</label>
                <select name="project_id" class="form-select">
                    <option value="">{{ __('ui.ph_none') }}</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ old('project_id',$budget->project_id)==$p->id?'selected':'' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label fw-semibold">{{ __('ui.lbl_amount') }} *</label>
                <div class="input-group">
                    <span class="input-group-text">{{ currency_sym($budget->currency) }}</span>
                    <input type="number" name="amount" step="0.01" min="0.01" class="form-control" value="{{ old('amount', $budget->amount) }}" required>
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">{{ __('ui.lbl_currency') }}</label>
                <select name="currency" class="form-select">
                    @foreach(['SAR','USD','EUR','GBP','AED','EGP','OMR','KWD','QAR','BHD'] as $cur)
                    <option value="{{ $cur }}" {{ old('currency',$budget->currency)==$cur?'selected':'' }}>{{ $cur }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">{{ __('ui.lbl_received_date') }} *</label>
                <input type="date" name="received_date" class="form-control" value="{{ old('received_date', $budget->received_date->format('Y-m-d')) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">{{ __('ui.lbl_status') }}</label>
                <select name="status" class="form-select">
                    <option value="active" {{ old('status',$budget->status)=='active'?'selected':'' }}>{{ __('ui.status_active') }}</option>
                    <option value="closed" {{ old('status',$budget->status)=='closed'?'selected':'' }}>{{ __('ui.status_closed') }}</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">{{ __('ui.lbl_notes') }}</label>
                <textarea name="notes" class="form-control" rows="3">{{ old('notes', $budget->notes) }}</textarea>
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary">{{ __('ui.btn_update_budget') }}</button>
                <a href="{{ route('budgets.show', $budget) }}" class="btn btn-outline-secondary">{{ __('ui.btn_cancel') }}</a>
            </div>
        </div>
    </form>
</div>
@endsection
