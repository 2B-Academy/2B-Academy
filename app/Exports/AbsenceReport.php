<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AbsenceReport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    protected $collection;
    protected $course;
    protected $group;


    function __construct($collection, $course, $group)
    {
        $this->collection = $collection;
        $this->course = $course;
        $this->group = $group;
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
            $row->machine_code,
            $row->name,
            $row->department_name,
            $this->course?->title,
            $this->group?->name,
            $this->course->category?->name,
            '0',
            $this->course?->hours,
        ];
    }


    public function headings(): array
    {
        return [
            'كود الموظف',
            'الأسم',
            'القسم',
            'الدورة التدريبية',
            'المجموعة',
            'الفئه',
            'عدد ساعات الحضور',
            'إجمالي ساعات الدورة التدريبية',
        ];
    }



}
