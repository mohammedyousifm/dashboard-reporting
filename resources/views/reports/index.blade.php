@extends('layouts.app')
@section('title', __('ui.pg_reports'))
@section('page-title', __('ui.pg_reports'))
@section('page-subtitle', __('ui.pg_reports_sub'))
@section('content')

<div class="row g-3 mb-4">
    @php
    $reportTypes = [
        ['title' => __('ui.nav_weekly'),  'desc' => __('ui.report_weekly_desc'),  'icon' => 'bi-calendar-week-fill',  'color' => '#4f46e5', 'bg' => '#ede9fe', 'route' => 'reports.weekly',  'key' => 'weekly'],
        ['title' => __('ui.nav_monthly'), 'desc' => __('ui.report_monthly_desc'), 'icon' => 'bi-calendar-month-fill', 'color' => '#059669', 'bg' => '#d1fae5', 'route' => 'reports.monthly', 'key' => 'monthly'],
        ['title' => __('ui.nav_yearly'),  'desc' => __('ui.report_yearly_desc'),  'icon' => 'bi-calendar-range-fill', 'color' => '#2563eb', 'bg' => '#dbeafe', 'route' => 'reports.yearly',  'key' => 'yearly'],
    ];
    @endphp

    @foreach($reportTypes as $r)
    <div class="col-md-4">
        <div class="chart-card h-100 d-flex flex-column">
            <div style="width:54px;height:54px;background:{{ $r['bg'] }};color:{{ $r['color'] }};border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;margin-bottom:.9rem">
                <i class="bi {{ $r['icon'] }}"></i>
            </div>
            <h6 class="fw-bold mb-1">{{ $r['title'] }}</h6>
            <p style="font-size:.82rem;color:#64748b;flex:1">{{ $r['desc'] }}</p>
            <div class="d-flex gap-2 mt-2">
                <a href="{{ route($r['route']) }}" class="btn btn-sm btn-primary flex-grow-1">
                    <i class="bi bi-eye me-1"></i>{{ __('ui.btn_view_report') }}
                </a>
                <a href="{{ route('reports.export', $r['key']) }}?format=pdf" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-filetype-pdf"></i>
                </a>
                <a href="{{ route('reports.export', $r['key']) }}?format=excel" class="btn btn-sm btn-outline-success">
                    <i class="bi bi-file-earmark-excel"></i>
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="chart-card">
    <div class="card-title"><i class="bi bi-funnel me-2" style="color:var(--primary)"></i>{{ __('ui.sec_quick_filter') }}</div>
    <form class="row g-3" method="GET" action="{{ route('reports.monthly') }}">
        <div class="col-md-3">
            <label class="form-label">{{ __('ui.lbl_report_type') }}</label>
            <select name="report_type" id="reportType" class="form-select">
                <option value="weekly">{{ __('ui.nav_weekly') }}</option>
                <option value="monthly" selected>{{ __('ui.nav_monthly') }}</option>
                <option value="yearly">{{ __('ui.nav_yearly') }}</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">{{ __('ui.lbl_month') }}</label>
            <select name="month" class="form-select">
                @for($m=1; $m<=12; $m++)
                    <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>{{ Carbon\Carbon::create(0,$m,1)->format('F') }}</option>
                @endfor
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">{{ __('ui.lbl_year') }}</label>
            <select name="year" class="form-select">
                @for($y=date('Y')-3; $y<=date('Y'); $y++)
                    <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">{{ __('ui.lbl_company') }}</label>
            <select name="company_id" class="form-select">
                <option value="">{{ __('ui.ph_all_companies') }}</option>
                @foreach($companies as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-1 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">{{ __('ui.btn_go') }}</button>
        </div>
    </form>
</div>

@endsection
@push('scripts')
<script>
document.getElementById('reportType').addEventListener('change', function() {
    const routes = {
        weekly: '{{ route("reports.weekly") }}',
        monthly: '{{ route("reports.monthly") }}',
        yearly: '{{ route("reports.yearly") }}',
    };
    this.closest('form').action = routes[this.value];
});
</script>
@endpush
