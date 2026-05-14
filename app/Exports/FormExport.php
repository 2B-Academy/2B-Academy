<?php

namespace App\Exports;

use App\Models\Course;
use App\Models\UserExam;
use App\Models\UserLectureProgress;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FormExport implements FromCollection, WithHeadings
{

    protected $form;

    public function __construct($form)
    {
        $this->form = $form;
    }



    public function collection()
    {
        $array = [];
        foreach ($this->form->users as $con)
        {
            $array[] = [
                $this->form->title,
                $con->machine_code,
                $con->name,
                $con->mark . '/'. $this->form->full_mark,
                $con->duration. ' دقيقة ',
                date('Y-m-d H:i A', strtotime($con->start_at)),
            ];
        }
        return collect($array);
    }


    /**
     * Define Excel headings
     */
    public function headings(): array
    {
        return [
            'اسم الأختبار',
            'كود الموظف',
            'الأسم',
            'الدرجة',
            'المدة المستغرقة',
            'تاريخ البدء',
        ];
    }

}
