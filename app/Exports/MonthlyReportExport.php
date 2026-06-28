<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MonthlyReportExport implements WithMultipleSheets
{
    public function __construct(private array $data) {}

    public function sheets(): array
    {
        $d = $this->data;

        return [
            new SingleSheetExport(
                'Weekly Task Trend',
                ['Week', 'Tasks Completed'],
                collect($d['weeklyTaskTrend'])->map(fn($r) => [$r['label'], $r['tasks']])->toArray()
            ),
            new SingleSheetExport(
                'Achievements',
                ['Achievement', 'Project', 'Type', 'Date'],
                collect($d['achievementList'])->map(fn($a) => [
                    $a->title,
                    $a->project?->name ?? '—',
                    ucfirst($a->type),
                    $a->achieved_date->format('M d, Y'),
                ])->toArray()
            ),
        ];
    }
}
