<?php

namespace App\Exports;

use App\Models\Course;
use App\Models\UserExam;
use App\Models\UserFormAnswers;
use App\Models\UserLectureProgress;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FormWrongQuestionsExport implements FromArray
{

    protected $data;

    public function __construct($userForms)
    {
        $this->data = $userForms;
    }

    public function array(): array
    {
        $rows = [];

        foreach ($this->data as $form) {

            // اسم المستخدم
            $rows[] = [$form->name];

            // الأسئلة الغلط
            foreach ($form->answers->where('is_true', 0) as $answer) {
                $rows[] = ['   - ' . $answer->question];
            }

            // سطر فاضي بين كل مستخدم
            $rows[] = [''];
        }

        return $rows;
    }

}
