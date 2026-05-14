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

class AttendanceReport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    use Exportable;

    protected $collection;
    protected $type;
    protected $total = 0;
    protected $total_hours = 0;


    function __construct($collection, $type)
    {
        $this->collection = $collection;
        $this->type = $type;
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
        $value = ($this->type == 'per_user') ?  round($row->attendance_hours) : round($row->total_attendance_hours);
        $this->total += $value;
        if ($this->type == 'per_user')
        {
            $value_a =  round($row->total_hours);
            $this->total_hours += $value_a;
            return [
                $row->employee_code,
                $row->employee_name,
                $row->user_department,
                $row->course_name,
                $row->group_name ?? '',
                $row->course_category_name,
                $row->sessions_count > 0 ? $row->sessions_count : 1,
                $row->user_attendance_count,
                round($value / $row->user_attendance_count),
                $value,
              //  $value_a,
            ];
        }
        elseif ($this->type == 'per_employee')
        {
            return [
                $row->user_machine_code,
                $row->name,
                $value,
            ];
        }
        return [
            $row->field,
            $value
        ];
    }


    public function headings(): array
    {
        if ($this->type == 'per_user')
        {
            return [
                'كود الموظف',
                'الأسم',
                'القسم',
                'الدورة التدريبية',
                'المجموعة',
                'الفئه',
                'عدد السيشنز المطلوبة',
                'حضر كام سيشن',
                'عدد ساعات السيشن الواحدة',
                'عدد ساعات الحضور',
              //  'إجمالي ساعات الدورة التدريبية',
            ];
        }elseif ($this->type == 'per_employee')
        {
            return [
                'الكود',
                'الأسم',
                'عدد ساعات الحضور',
            ];
        }
        return [
            'الفئه',
            'عدد الساعات'
        ];
    }


    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow() + 1;


                if ($this->type === 'per_user') {
                    $sheet->setCellValue('A' . $lastRow, 'Total');
                    $sheet->setCellValue('J' . $lastRow, (int) $this->total);
                  //  $sheet->setCellValue('K' . $lastRow, (int) $this->total_hours);
                    $sheet->mergeCells('A' . $lastRow . ':H' . $lastRow);
                    $range = 'A' . $lastRow . ':J' . $lastRow;
                } elseif ($this->type === 'per_employee') {
                    $sheet->setCellValue('A' . $lastRow, 'Total');
                    $sheet->setCellValue('C' . $lastRow, (int) $this->total);
                    $sheet->mergeCells('A' . $lastRow . ':B' . $lastRow);
                    $range = 'A' . $lastRow . ':C' . $lastRow;
                } else {
                    $sheet->setCellValue('A' . $lastRow, 'Total');
                    $sheet->setCellValue('B' . $lastRow, $this->total);
                    $range = 'A' . $lastRow . ':B' . $lastRow;
                }

                $sheet->getStyle($range)->getFont()->setBold(true);

                $sheet->getStyle($range)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFDFF0D8');
            },
        ];
    }


}
