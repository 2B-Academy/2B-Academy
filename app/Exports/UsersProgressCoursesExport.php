<?php

namespace App\Exports;

use App\Models\Course;
use App\Models\CourseSection;
use App\Models\User;
use App\Models\UserExam;
use App\Models\UserLectureProgress;
use App\Models\UsersCourse;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersProgressCoursesExport implements FromCollection, WithHeadings
{

    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }



    public function collection()
    {
        $selectedCourse =  $this->filters['course_id'] ?? null; // filter course
        $selectedUser   =   $this->filters['user_id'] ?? null;  // filter user
        $selectedGroup   =   $this->filters['group_id'] ?? null;  // filter user


        $query = User::query()->select([
                'users.id',
                'users.machine_code',
                'users.name',
                'users.department_name',
                'courses.title as course_title',
                'courses.course_type as course_type',
                'courses.for_public as for_public',
                'course_sections.name as group_name',
                'courses.id as course_id',
            ])
            ->join('users_courses', 'users.id', '=', 'users_courses.user_id')
            ->join('courses', 'courses.id', '=', 'users_courses.course_id')
            ->leftJoin('course_sections', 'course_sections.id', '=', 'users_courses.group_id')
            ->when($selectedCourse, function ($q) use ($selectedCourse) {
                $q->where('courses.id', $selectedCourse);
            })
            ->when($selectedGroup, function ($q) use ($selectedGroup) {
                $q->where('users_courses.group_id', $selectedGroup);
            })
            ->when($selectedUser, function ($q) use ($selectedUser) {
                $q->where('users.id', $selectedUser);
            })->with(['exams', 'evaluations']);
        $results = $query->get();
        return $results->map(function ($user) {
            $finalExam = $user->exams->first();
            $evaluation = $user->evaluations->first();
            $degree = $finalExam
                ? $finalExam->user_degree . ' / ' . $finalExam->exam?->degree
                : '';
            // نسبة التقدم
            if ($finalExam && !is_null($finalExam->user_degree)) {
                $progress = 100;
            } elseif ($evaluation) {
                $progress = 100;
            } else {
                $progress = 0;
            }
            return [
                'code'       => $user->machine_code,
                'user'       => $user->name,
                'department' => $user->department_name,
                'course'     => $user->course_title,
                'group'      => $user->group_name,
                'course_type'=> $user->course_type,
                'user_degree'=> $degree,
                'progress'   => $progress. '%',
            ];
        });

    }


    /**
     * Define Excel headings
     */
    public function headings(): array
    {
        return [
            'كود الموظف',
            'الأسم',
            'القسم',
            'الدورة التدريبية',
            'المجموعة',
            'نوع الدورة التدريبية',
            'نتيجة الأختبار النهائي',
            'نسبة التقدم (%)',
        ];
    }

}
