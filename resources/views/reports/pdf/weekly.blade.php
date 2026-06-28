<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e293b; margin: 30px; }
    h1 { font-size: 20px; color: #4f46e5; margin: 0 0 4px; }
    .subtitle { color: #64748b; font-size: 11px; margin-bottom: 18px; }
    .summary { background: #ede9fe; border-left: 4px solid #4f46e5; padding: 10px 14px; margin-bottom: 20px; font-size: 10px; line-height: 1.5; }
    h2 { font-size: 12px; color: #374151; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; margin: 20px 0 8px; text-transform: uppercase; letter-spacing: .5px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
    th { background: #f8fafc; text-align: left; padding: 6px 8px; font-size: 10px; color: #64748b; border-bottom: 2px solid #e2e8f0; }
    td { padding: 5px 8px; border-bottom: 1px solid #f1f5f9; font-size: 10px; }
    tfoot td { font-weight: bold; background: #f8fafc; border-top: 2px solid #e2e8f0; }
    .footer { margin-top: 30px; color: #94a3b8; font-size: 9px; text-align: right; border-top: 1px solid #f1f5f9; padding-top: 8px; }
</style>
</head>
<body>
<h1>Weekly Report</h1>
<div class="subtitle">{{ $date->format('F d') }} – {{ $weekEnd->format('F d, Y') }}</div>

<div class="summary">{{ $summary }}</div>

<h2>Tasks This Week</h2>
<table>
    <thead><tr><th>Task</th><th>Project</th><th>Priority</th><th>Status</th><th>Due</th></tr></thead>
    <tbody>
    @forelse($taskList as $t)
    <tr>
        <td>{{ $t->title }}</td>
        <td>{{ $t->project?->name ?? '—' }}</td>
        <td>{{ ucfirst($t->priority) }}</td>
        <td>{{ ucfirst(str_replace('_',' ',$t->status)) }}</td>
        <td>{{ $t->due_date?->format('M d') ?? '—' }}</td>
    </tr>
    @empty
    <tr><td colspan="5">No tasks due or completed this week</td></tr>
    @endforelse
    </tbody>
</table>

@if($achievementList->isNotEmpty())
<h2>Achievements This Week</h2>
<table>
    <thead><tr><th>Achievement</th><th>Type</th><th>Date</th></tr></thead>
    <tbody>
    @foreach($achievementList as $a)
    <tr>
        <td>{{ $a->title }}</td>
        <td>{{ ucfirst($a->type) }}</td>
        <td>{{ $a->achieved_date->format('M d, Y') }}</td>
    </tr>
    @endforeach
    </tbody>
</table>
@endif

<div class="footer">Generated on {{ now()->format('F d, Y \a\t g:i A') }}</div>
</body>
</html>
