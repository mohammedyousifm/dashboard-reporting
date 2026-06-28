<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class WeeklyReportExport implements WithMultipleSheets
{
    public function __construct(private array $data) {}

    public function sheets(): array
    {
        $d = $this->data;

        return [
            new SingleSheetExport(
                'Tasks This Week',
                ['Task', 'Project', 'Priority', 'Status', 'Due Date'],
                collect($d['taskList'])->map(fn($t) => [
                    $t->title,
                    $t->project?->name ?? '—',
                    ucfirst($t->priority),
                    ucfirst(str_replace('_', ' ', $t->status)),
                    $t->due_date?->format('M d, Y') ?? '—',
                ])->toArray()
            ),
            new SingleSheetExport(
                'Achievements',
                ['Achievement', 'Type', 'Date'],
                collect($d['achievementList'])->map(fn($a) => [
                    $a->title,
                    ucfirst($a->type),
                    $a->achieved_date->format('M d, Y'),
                ])->toArray()
            ),
        ];
    }
}
