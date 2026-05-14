<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class CategoryEvaluationsExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $categories;

    public function __construct($categories)
    {
        $this->categories = $categories;
    }

    public function collection()
    {
        $data = collect();

        foreach ($this->categories as $category) {
            // إضافة صف عنوان الفئة
            $data->push([
                'فئة التقييم' => $category['evaluation_category_name'],
                'اسم المحاضر' => '',
                'متوسط التقييم' => ''
            ]);

            // إضافة المحاضرين
            foreach ($category['instructors'] as $instructor) {
                $data->push([
                    'فئة التقييم' => '',
                    'اسم المحاضر' => $instructor['instructor_name'],
                    'متوسط التقييم' => $instructor['avg_rate']
                ]);
            }

            // صف فارغ بين الفئات
            $data->push(['', '', '']);
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'فئة التقييم',
            'اسم المحاضر',
            'متوسط التقييم'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $currentRow = 2; // بعد الـ header
        $styles = [
            1 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ]
        ];

        foreach ($this->categories as $category) {
            // تنسيق صف الفئة
            $styles[$currentRow] = [
                'font' => ['bold' => true, 'size' => 11],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'D9E1F2']
                ]
            ];

            $currentRow++; // الانتقال للمحاضرين

            // تخطي صفوف المحاضرين
            $currentRow += count($category['instructors']);

            // تخطي الصف الفارغ
            $currentRow++;
        }

        // إضافة حدود لكل الخلايا
        $lastRow = $currentRow - 1;
        $sheet->getStyle('A1:C' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // محاذاة الأرقام للوسط
        $sheet->getStyle('C2:C' . $lastRow)
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return $styles;
    }
}
