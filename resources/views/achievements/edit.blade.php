@extends('layouts.app')
@section('title', __('ui.pg_edit_achievement'))
@section('page-title', __('ui.pg_edit_achievement'))
@section('content')
<div class="chart-card" style="max-width:680px">
    <div class="card-title"><i class="bi bi-trophy me-2" style="color:#ca8a04"></i>{{ __('ui.pg_edit_achievement') }}</div>
    <form action="{{ route('achievements.update', $achievement) }}" method="POST">
        @csrf @method('PUT')
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label">{{ __('ui.lbl_title') }} *</label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $achievement->title) }}" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('ui.lbl_project') }}</label>
                <select name="project_id" class="form-select">
                    <option value="">{{ __('ui.ph_no_category') }}</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ old('project_id', $achievement->project_id) == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('ui.lbl_type') }}</label>
                <select name="type" class="form-select">
                    @foreach(['milestone','certification','award','other'] as $type)
                    <option value="{{ $type }}" {{ old('type', $achievement->type) == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('ui.lbl_achieved_date') }} *</label>
                <input type="date" name="achieved_date" class="form-control @error('achieved_date') is-invalid @enderror" value="{{ old('achieved_date', $achievement->achieved_date->toDateString()) }}" required>
                @error('achieved_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label">{{ __('ui.lbl_description') }}</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $achievement->description) }}</textarea>
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary">{{ __('ui.btn_save_achievement') }}</button>
                <a href="{{ route('achievements.index') }}" class="btn btn-outline-secondary">{{ __('ui.btn_cancel') }}</a>
            </div>
        </div>
    </form>
</div>
@endsection
