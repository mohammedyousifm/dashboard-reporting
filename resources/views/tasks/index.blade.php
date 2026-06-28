@extends('layouts.app')
@section('title', __('ui.pg_tasks'))
@section('page-title', __('ui.pg_tasks'))
@section('topbar-actions')
<a href="{{ route('tasks.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg me-1"></i>{{ __('ui.btn_add_task') }}</a>
@endsection
@section('content')

<div class="filter-card no-print">
    <form class="row g-2 align-items-end" method="GET">
        <div class="col-md-3">
            <label class="form-label">{{ __('ui.lbl_status') }}</label>
            <select name="status" class="form-select form-select-sm">
                <option value="">{{ __('ui.ph_all_statuses') }}</option>
                @foreach(['pending','in_progress','completed','cancelled'] as $s)
                <option value="{{ $s }}" {{ request('status')==$s?'selected':'' }}>{{ __('ui.status_'.$s) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">{{ __('ui.lbl_project') }}</label>
            <select name="project_id" class="form-select form-select-sm">
                <option value="">{{ __('ui.ph_all_projects') }}</option>
                @foreach($projects as $p)
                <option value="{{ $p->id }}" {{ request('project_id')==$p->id?'selected':'' }}>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto d-flex gap-1">
            <button class="btn btn-sm btn-primary">{{ __('ui.btn_filter') }}</button>
            <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-outline-secondary">{{ __('ui.btn_clear') }}</a>
        </div>
    </form>
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr>
                <th>{{ __('ui.th_task') }}</th>
                <th>{{ __('ui.th_project') }}</th>
                <th>{{ __('ui.th_company') }}</th>
                <th>{{ __('ui.th_category') }}</th>
                <th>{{ __('ui.th_priority') }}</th>
                <th>{{ __('ui.th_status') }}</th>
                <th>{{ __('ui.th_due') }}</th>
                <th></th>
            </tr></thead>
            <tbody>
            @forelse($tasks as $t)
            <tr>
                <td class="fw-semibold">{{ $t->title }}</td>
                <td style="color:#64748b;font-size:.83rem">{{ $t->project?->name ?? '—' }}</td>
                <td style="color:#64748b;font-size:.83rem">{{ $t->project?->company?->name ?? '—' }}</td>
                <td>
                    @if($t->category)
                    <span class="badge" style="background:{{ $t->category->color }}20;color:{{ $t->category->color }}">{{ $t->category->name }}</span>
                    @else <span style="color:#94a3b8">—</span>
                    @endif
                </td>
                <td>
                    @php $pa=['high'=>['#fee2e2','#991b1b'],'medium'=>['#fef3c7','#92400e'],'low'=>['#d1fae5','#065f46']] @endphp
                    @if(isset($pa[$t->priority]))
                    <span class="badge" style="background:{{ $pa[$t->priority][0] }};color:{{ $pa[$t->priority][1] }}">{{ __('ui.priority_'.$t->priority) }}</span>
                    @endif
                </td>
                <td><span class="badge status-{{ $t->status }}">{{ __('ui.status_'.$t->status) }}</span></td>
                <td style="color:#64748b;font-size:.82rem">{{ $t->due_date?->format('M d, Y') ?? '—' }}</td>
                <td>
                    <div class="d-flex gap-1 justify-content-end">
                        <a href="{{ route('tasks.edit', $t) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('tasks.destroy', $t) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('ui.confirm_delete') }}')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="table-empty"><i class="bi bi-check2-square"></i>{{ __('ui.empty_tasks') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 border-top">{{ $tasks->links() }}</div>
</div>
@endsection
