<?php

namespace App\Services;

use App\Models\UserCourseEvaluation;
use App\Models\UserExam;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CertificateService
{
    public function paginate(int $perPage = 20, ?int $courseId = null): LengthAwarePaginator
    {
        $examCerts = UserExam::with([
                'course:id,title,title_for_certificate,is_evaluate',
                'user:id,machine_code,name,department_name',
                'exam:id,is_final,degree',
            ])
            ->whereHas('course', fn ($q) => $q->where('certificate', true)->where('is_evaluate', false))
            ->whereHas('exam', fn ($q) => $q->where('is_final', true))
            ->where('status', 'success')
            ->when($courseId, fn ($q) => $q->where('course_id', $courseId))
            ->get()
            ->map(fn ($ue) => $this->formatExamCert($ue));

        $evalCerts = UserCourseEvaluation::with([
                'course:id,title,title_for_certificate,is_evaluate',
                'user:id,machine_code,name,department_name',
            ])
            ->whereHas('course', fn ($q) => $q->where('certificate', true)->where('is_evaluate', true))
            ->when($courseId, fn ($q) => $q->where('course_id', $courseId))
            ->get()
            ->unique(fn ($item) => $item->user_id . '-' . $item->course_id)
            ->map(fn ($uce) => $this->formatEvalCert($uce));

        $merged  = $examCerts->merge($evalCerts)->sortByDesc('created_at')->values();
        $page    = request()->input('page', 1);
        $slice   = $merged->forPage($page, $perPage);

        return new LengthAwarePaginator(
            $slice->values(),
            $merged->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    private function formatExamCert(UserExam $ue): array
    {
        return [
            'type'          => 'exam',
            'user'          => [
                'id'              => $ue->user?->id,
                'name'            => $ue->user?->name,
                'machine_code'    => $ue->user?->machine_code,
                'department_name' => $ue->user?->department_name,
            ],
            'course'        => [
                'id'                     => $ue->course?->id,
                'title'                  => $ue->course?->getTranslations('title'),
                'title_for_certificate'  => $ue->course?->getTranslations('title_for_certificate'),
            ],
            'user_degree'   => $ue->user_degree,
            'total_degree'  => $ue->exam?->degree,
            'created_at'    => $ue->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    private function formatEvalCert(UserCourseEvaluation $uce): array
    {
        return [
            'type'          => 'evaluation',
            'user'          => [
                'id'              => $uce->user?->id,
                'name'            => $uce->user?->name,
                'machine_code'    => $uce->user?->machine_code,
                'department_name' => $uce->user?->department_name,
            ],
            'course'        => [
                'id'                     => $uce->course?->id,
                'title'                  => $uce->course?->getTranslations('title'),
                'title_for_certificate'  => $uce->course?->getTranslations('title_for_certificate'),
            ],
            'user_degree'   => null,
            'total_degree'  => null,
            'created_at'    => $uce->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
