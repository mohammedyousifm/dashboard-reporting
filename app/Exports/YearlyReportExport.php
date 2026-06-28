<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class YearlyReportExport implements WithMultipleSheets
{
    public function __construct(private array $data) {}

    public function sheets(): array
    {
        $d = $this->data;

        return [
            new SingleSheetExport(
                'Monthly Breakdown',
                ['Month', 'Tasks Completed'],
                collect($d['monthlyTrend'])->map(fn($r) => [$r['label'], $r['tasks_completed']])->toArray()
            ),
            new SingleSheetExport(
                'Project Status',
                ['Status', 'Count'],
                collect($d['projectStatusBreakdown'])->map(fn($r) => [
                    ucfirst(str_replace('_', ' ', $r->status)),
                    $r->count,
                ])->toArray()
            ),
        ];
    }
}
