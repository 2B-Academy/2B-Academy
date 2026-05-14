<?php

namespace App\Exports;

use App\Models\Course;
use App\Models\UserExam;
use App\Models\UserFormAnswers;
use App\Models\UserLectureProgress;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FormTextQuestionsExport implements FromCollection, WithHeadings
{

    protected $form;

    public function __construct($form)
    {
        $this->form = $form;
    }



    public function collection()
    {
        $form = $this->form;
        return UserFormAnswers::with(['mainQuestion','userForm'])
            ->whereHas('mainQuestion', function ($q) use ($form) {
                $q->where('form_id', $form->id)->where('type', 'text');
            })
            ->get()
            ->map(fn($con) => [
                $form->title,
                $con->userForm?->name ?? 'N/A',
                $con->mainQuestion?->question ?? 'N/A',
                $con->answer,
                $con->created_at,
            ]);
    }


    /**
     * Define Excel headings
     */
    public function headings(): array
    {
        return [
            'الإختبار',
            'الأسم',
            'السؤال',
            'الإجابة',
            'التاريخ والوقت',
        ];
    }

}
