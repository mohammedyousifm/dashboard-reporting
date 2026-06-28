@extends('layouts.app')
@section('title', __('ui.pg_add_task'))
@section('page-title', __('ui.pg_add_task'))
@section('content')
<div class="chart-card" style="max-width:720px">
    <form action="{{ route('tasks.store') }}" method="POST">
        @csrf
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label fw-semibold">{{ __('ui.lbl_task_title') }} *</label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">{{ __('ui.lbl_project') }} *</label>
                <select name="project_id" class="form-select @error('project_id') is-invalid @enderror" required>
                    <option value="">{{ __('ui.ph_select_project') }}</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ old('project_id') == $p->id ? 'selected' : '' }}>{{ $p->name }} — {{ $p->company?->name }}</option>
                    @endforeach
                </select>
                @error('project_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">{{ __('ui.lbl_category') }}</label>
                <select name="category_id" class="form-select">
                    <option value="">{{ __('ui.ph_none') }}</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">{{ __('ui.lbl_priority') }}</label>
                <select name="priority" class="form-select">
                    @foreach(['low','medium','high'] as $pr)
                    <option value="{{ $pr }}" {{ old('priority','medium') == $pr ? 'selected' : '' }}>{{ __('ui.priority_'.$pr) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">{{ __('ui.lbl_status') }}</label>
                <select name="status" class="form-select">
                    @foreach(['pending','in_progress','completed','cancelled'] as $s)
                    <option value="{{ $s }}" {{ old('status','pending') == $s ? 'selected' : '' }}>{{ __('ui.status_'.$s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">{{ __('ui.lbl_due_date') }}</label>
                <input type="date" name="due_date" class="form-control" value="{{ old('due_date') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">{{ __('ui.lbl_completed_date') }}</label>
                <input type="date" name="completed_date" class="form-control" value="{{ old('completed_date') }}">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">{{ __('ui.lbl_description') }}</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary">{{ __('ui.btn_save_task') }}</button>
                <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">{{ __('ui.btn_cancel') }}</a>
            </div>
        </div>
    </form>
</div>
@endsection
