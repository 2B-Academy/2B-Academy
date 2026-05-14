<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class EvaluationsTextQuestionsExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    protected $collection;


    function __construct($collection)
    {
        $this->collection = $collection;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->collection;
    }

    public function map($row): array
    {
        return [
            $row->evaluation_title,
            $row->answer,
            $row->evaluation_category_name,
            $row->instructor_name,
            $row->course_name,
        ];
    }


    public function headings(): array
    {
        return [
            'السؤال',
            'الإجابة',
            'القسم',
            'المحاضر',
            'الدورة التدريبية',
        ];

    }

}
