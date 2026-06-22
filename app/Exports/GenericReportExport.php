<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Generic single-sheet exporter for the admin Reports surface.
 *
 * Accepts a heading row + a list of data rows and produces a clean XLSX
 * with a bold/grey header strip and auto-sized columns. The same class
 * powers every report type — no need for one Export per dataset.
 */
class GenericReportExport implements FromArray, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    /**
     * @param  list<string>                       $headings
     * @param  list<list<string|int|float|null>>  $rows
     */
    public function __construct(
        private readonly array $headings,
        private readonly array $rows,
        private readonly string $sheetTitle = 'Report',
    ) {}

    public function array(): array
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function title(): string
    {
        // Excel limits sheet names to 31 chars and disallows certain chars.
        return substr(preg_replace('/[\\\\\\/\\?\\*\\[\\]:]/', ' ', $this->sheetTitle) ?: 'Report', 0, 31);
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FF0C2427']],
                'fill' => [
                    'fillType'   => 'solid',
                    'startColor' => ['argb' => 'FFF5F5F5'],
                ],
            ],
        ];
    }
}
