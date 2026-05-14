<?php

namespace App\Exports;

use App\Models\Course;
use App\Models\FormQuestion;
use App\Models\UserExam;
use App\Models\UserFormAnswers;
use App\Models\UserLectureProgress;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FormQuestionsExport implements FromCollection, WithHeadings
{

    protected $form;

    public function __construct($form)
    {
        $this->form = $form;
    }



    public function collection()
    {
        $form = $this->form;
        $questions = FormQuestion::where('form_id', $form->id)->where('type', '!=', 'text')
            ->withCount([
                'userFormAnswers as correct_answers_count' => fn($q) => $q->where('is_true', 1),
                'userFormAnswers as wrong_answers_count'   => fn($q) => $q->where('is_true', 0),
            ])
            ->orderByDesc('correct_answers_count')
            ->get();

        return $questions->map(function ($q) use ($form) {
                    $total = $q->correct_answers_count + $q->wrong_answers_count;
                    $correct_percentage = $total ? round(($q->correct_answers_count / $total) * 100, 2) : 0;
                    $wrong_percentage   = $total ? round(($q->wrong_answers_count / $total) * 100, 2) : 0;
                    return [
                        $form->title,
                        $q->question,
                        $q->type,
                        $q->correct_answers_count,
                        $q->wrong_answers_count,
                        $correct_percentage.' %',
                        $wrong_percentage.' %',
                        $q->created_at,
                    ];
        })->sortByDesc('correct_percentage');
    }


    /**
     * Define Excel headings
     */
    public function headings(): array
    {
        return [
            'الإختبار',
            'السؤال',
            'نوع السؤال',
            'عدد الإجابات الصحيحة',
            'عدد الإجابات الخاطئة',
            'نسبة الإجابات الصحيحة (%)',
            'نسبة الإجابات الخاطئة (%)',
            'التاريخ والوقت',
        ];
    }

}
