@extends('layouts.app')
@section('title', __('ui.pg_edit_company'))
@section('page-title', __('ui.pg_edit_company') . ': ' . $company->name)
@section('content')
<div class="chart-card" style="max-width:640px">
    <form action="{{ route('companies.update', $company) }}" method="POST">
        @csrf @method('PUT')
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label fw-semibold">{{ __('ui.lbl_name') }} *</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $company->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">{{ __('ui.lbl_industry') }}</label>
                <input type="text" name="industry" class="form-control" value="{{ old('industry', $company->industry) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">{{ __('ui.lbl_status') }}</label>
                <select name="status" class="form-select">
                    <option value="active" {{ $company->status === 'active' ? 'selected' : '' }}>{{ __('ui.status_active') }}</option>
                    <option value="inactive" {{ $company->status === 'inactive' ? 'selected' : '' }}>{{ __('ui.status_inactive') }}</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">{{ __('ui.lbl_contact_email') }}</label>
                <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email', $company->contact_email) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">{{ __('ui.lbl_contact_phone') }}</label>
                <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone', $company->contact_phone) }}">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">{{ __('ui.lbl_address') }}</label>
                <textarea name="address" class="form-control" rows="2">{{ old('address', $company->address) }}</textarea>
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary">{{ __('ui.btn_update_company') }}</button>
                <a href="{{ route('companies.index') }}" class="btn btn-outline-secondary">{{ __('ui.btn_cancel') }}</a>
            </div>
        </div>
    </form>
</div>
@endsection
