@extends('layouts.app')
@section('title', __('ui.pg_achievements'))
@section('page-title', __('ui.pg_achievements'))
@section('topbar-actions')
<a href="{{ route('achievements.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg me-1"></i>{{ __('ui.btn_add_achievement') }}</a>
@endsection
@section('content')
<div class="table-card">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr>
                <th>{{ __('ui.th_achievement') }}</th>
                <th>{{ __('ui.th_project') }}</th>
                <th>{{ __('ui.th_type') }}</th>
                <th>{{ __('ui.th_date') }}</th>
                <th></th>
            </tr></thead>
            <tbody>
            @forelse($achievements as $a)
            <tr>
                <td class="fw-semibold">{{ $a->title }}</td>
                <td style="color:#64748b;font-size:.83rem">{{ $a->project?->name ?? '—' }}</td>
                <td><span class="badge status-active">{{ ucfirst($a->type) }}</span></td>
                <td style="color:#64748b;font-size:.83rem">{{ $a->achieved_date->format('M d, Y') }}</td>
                <td>
                    <div class="d-flex gap-1 justify-content-end">
                        <a href="{{ route('achievements.show', $a) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('achievements.edit', $a) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('achievements.destroy', $a) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('ui.confirm_delete') }}')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="table-empty"><i class="bi bi-trophy"></i>{{ __('ui.empty_achievements') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 border-top">{{ $achievements->links() }}</div>
</div>
@endsection
