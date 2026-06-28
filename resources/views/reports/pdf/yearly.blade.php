<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e293b; margin: 30px; }
    h1 { font-size: 20px; color: #2563eb; margin: 0 0 4px; }
    .subtitle { color: #64748b; font-size: 11px; margin-bottom: 18px; }
    .summary { background: #dbeafe; border-left: 4px solid #2563eb; padding: 10px 14px; margin-bottom: 20px; font-size: 10px; line-height: 1.5; }
    h2 { font-size: 12px; color: #374151; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; margin: 20px 0 8px; text-transform: uppercase; letter-spacing: .5px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
    th { background: #f8fafc; text-align: left; padding: 6px 8px; font-size: 10px; color: #64748b; border-bottom: 2px solid #e2e8f0; }
    td { padding: 5px 8px; border-bottom: 1px solid #f1f5f9; font-size: 10px; }
    tfoot td { font-weight: bold; background: #f8fafc; border-top: 2px solid #e2e8f0; }
    .footer { margin-top: 30px; color: #94a3b8; font-size: 9px; text-align: right; border-top: 1px solid #f1f5f9; padding-top: 8px; }
</style>
</head>
<body>
<h1>Yearly Report — {{ $year }}</h1>
<div class="subtitle">Annual Summary</div>

<div class="summary">{{ $summary }}</div>

<h2>Monthly Breakdown</h2>
<table>
    <thead><tr><th>Month</th><th>Tasks Completed</th></tr></thead>
    <tbody>
    @foreach($monthlyTrend as $mt)
    <tr>
        <td>{{ $mt['label'] }}</td>
        <td>{{ $mt['tasks_completed'] ?: '—' }}</td>
    </tr>
    @endforeach
    </tbody>
    <tfoot><tr><td>Total</td><td>{{ $tasksCompleted }}</td></tr></tfoot>
</table>

<h2>Project Status</h2>
<table>
    <thead><tr><th>Status</th><th>Count</th></tr></thead>
    <tbody>
    @forelse($projectStatusBreakdown as $ps)
    <tr>
        <td>{{ ucfirst(str_replace('_',' ',$ps->status)) }}</td>
        <td>{{ $ps->count }}</td>
    </tr>
    @empty
    <tr><td colspan="2">No projects</td></tr>
    @endforelse
    </tbody>
</table>

<div class="footer">Generated on {{ now()->format('F d, Y \a\t g:i A') }}</div>
</body>
</html>
