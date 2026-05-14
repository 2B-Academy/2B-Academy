<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class EvaluationReportExportByQuestions implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $pivot;
    protected $questions;
    protected $grandTotal;

    public function __construct($pivot, $questions, $grandTotal)
    {
        $this->pivot = $pivot;
        $this->questions = $questions;
        $this->grandTotal = $grandTotal;
    }

    public function collection()
    {
        $data = collect();

        // بيانات المدرسين
        foreach ($this->pivot as $instructor => $values) {
            $row = [$instructor];

            foreach ($this->questions as $question) {
                $row[] = $values['questions'][$question] ?? '-';
            }

            $row[] = $values['overall'];

            $data->push($row);
        }

        // إضافة صف Grand Total
        $grandTotalRow = ['Grand Total'];
        foreach ($this->questions as $question) {
            $grandTotalRow[] = $this->grandTotal['questions'][$question] ?? '-';
        }
        $grandTotalRow[] = $this->grandTotal['overall'];

        $data->push($grandTotalRow);

        return $data;
    }

    public function headings(): array
    {
        $headers = ['المحاضرين'];

        // إضافة أسماء الأسئلة
        foreach ($this->questions as $question) {
            $headers[] = 'Average of ' . $question;
        }

        $headers[] = 'Overall';

        return $headers;
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = count($this->pivot) + 2; // +2 للـ header والـ Grand Total
        $lastColumn = count($this->questions) + 2; // +2 للـ Row Labels والـ Overall

        return [
            // تنسيق الـ Header
            1 => [
                'font' => ['bold' => true, 'size' => 11],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ],

            // تنسيق صف Grand Total
            $lastRow => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'B4C7E7']
                ]
            ],
        ];
    }
}
