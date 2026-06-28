@extends('layouts.app')
@section('title', __('ui.pg_add_project'))
@section('page-title', __('ui.pg_add_project'))
@section('content')
<div class="chart-card" style="max-width:740px">
    <div class="card-title"><i class="bi bi-kanban me-2" style="color:var(--primary)"></i>{{ __('ui.pg_add_project') }}</div>
    <form action="{{ route('projects.store') }}" method="POST">
        @csrf
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label">{{ __('ui.lbl_project_name') }} *</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('ui.lbl_company') }} *</label>
                <select name="company_id" class="form-select @error('company_id') is-invalid @enderror" required>
                    <option value="">{{ __('ui.ph_select_company') }}</option>
                    @foreach($companies as $c)
                        <option value="{{ $c->id }}" {{ old('company_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
                @error('company_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('ui.lbl_category') }}</label>
                <select name="category_id" class="form-select">
                    <option value="">{{ __('ui.ph_no_category') }}</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('ui.lbl_status') }}</label>
                <select name="status" class="form-select">
                    <option value="active">{{ __('ui.status_active') }}</option>
                    <option value="on_hold">{{ __('ui.status_on_hold') }}</option>
                    <option value="completed">{{ __('ui.status_completed') }}</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('ui.lbl_start_date') }}</label>
                <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('ui.lbl_end_date') }}</label>
                <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}">
            </div>
            <div class="col-12">
                <label class="form-label">{{ __('ui.lbl_description') }}</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary">{{ __('ui.btn_save_project') }}</button>
                <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">{{ __('ui.btn_cancel') }}</a>
            </div>
        </div>
    </form>
</div>
@endsection
