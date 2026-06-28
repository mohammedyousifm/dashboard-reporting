@extends('layouts.app')
@section('title', __('ui.pg_edit_project'))
@section('page-title', __('ui.pg_edit_project') . ': ' . $project->name)
@section('content')
<div class="chart-card" style="max-width:720px">
    <form action="{{ route('projects.update', $project) }}" method="POST">
        @csrf @method('PUT')
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label fw-semibold">{{ __('ui.lbl_project_name') }} *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $project->name) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">{{ __('ui.lbl_company') }} *</label>
                <select name="company_id" class="form-select" required>
                    @foreach($companies as $c)
                        <option value="{{ $c->id }}" {{ old('company_id', $project->company_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">{{ __('ui.lbl_category') }}</label>
                <select name="category_id" class="form-select">
                    <option value="">{{ __('ui.ph_no_category') }}</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $project->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">{{ __('ui.lbl_status') }}</label>
                <select name="status" class="form-select">
                    @foreach(['active','on_hold','completed'] as $s)
                    <option value="{{ $s }}" {{ old('status', $project->status) === $s ? 'selected' : '' }}>{{ __('ui.status_'.$s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">{{ __('ui.lbl_start_date') }}</label>
                <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $project->start_date?->format('Y-m-d')) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">{{ __('ui.lbl_end_date') }}</label>
                <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $project->end_date?->format('Y-m-d')) }}">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">{{ __('ui.lbl_description') }}</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $project->description) }}</textarea>
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary">{{ __('ui.btn_update_project') }}</button>
                <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">{{ __('ui.btn_cancel') }}</a>
            </div>
        </div>
    </form>
</div>
@endsection
