@extends('layouts.app')
@section('title', __('ui.pg_companies'))
@section('page-title', __('ui.pg_companies'))
@section('topbar-actions')
<a href="{{ route('companies.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg me-1"></i>{{ __('ui.btn_add_company') }}</a>
@endsection
@section('content')
<div class="table-card">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr>
                <th>{{ __('ui.th_name') }}</th>
                <th>{{ __('ui.th_industry') }}</th>
                <th>{{ __('ui.th_projects') }}</th>
                <th>{{ __('ui.th_status') }}</th>
                <th>{{ __('ui.th_contact') }}</th>
                <th></th>
            </tr></thead>
            <tbody>
            @forelse($companies as $c)
            <tr>
                <td class="fw-semibold"><a href="{{ route('companies.show', $c) }}" class="text-decoration-none text-dark">{{ $c->name }}</a></td>
                <td style="color:#64748b;font-size:.84rem">{{ $c->industry ?? '—' }}</td>
                <td><span class="badge bg-primary bg-opacity-10 text-primary">{{ $c->projects_count }}</span></td>
                <td><span class="badge {{ $c->status === 'active' ? 'status-active' : 'status-inactive' }}">{{ __('ui.status_'.$c->status) }}</span></td>
                <td style="color:#64748b;font-size:.82rem">{{ $c->contact_email ?? '—' }}</td>
                <td>
                    <div class="d-flex gap-1 justify-content-end">
                        <a href="{{ route('companies.edit', $c) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('companies.destroy', $c) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('ui.confirm_delete') }}')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="table-empty"><i class="bi bi-building"></i>{{ __('ui.empty_companies') }} <a href="{{ route('companies.create') }}">{{ __('ui.add_one') }}</a></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3 border-top">{{ $companies->links() }}</div>
</div>
@endsection
