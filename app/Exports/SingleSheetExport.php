<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class SingleSheetExport implements FromArray, WithHeadings, WithTitle
{
    public function __construct(
        private string $sheetTitle,
        private array $headers,
        private array $rows
    ) {}

    public function title(): string { return $this->sheetTitle; }
    public function headings(): array { return $this->headers; }
    public function array(): array { return $this->rows; }
}
