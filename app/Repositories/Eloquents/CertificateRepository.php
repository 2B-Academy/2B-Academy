<?php

namespace App\Repositories\Eloquents;

use App\Models\UserCourseEvaluation;
use App\Models\UserExam;
use App\Repositories\Contracts\CertificateRepositoryInterface;
use Illuminate\Support\Collection;

class CertificateRepository implements CertificateRepositoryInterface
{
    public function getExamCertificates(?int $courseId): Collection
    {
        return UserExam::with([
                'course:id,title,title_for_certificate,is_evaluate',
                'user:id,machine_code,name,department_name',
                'exam:id,is_final,degree',
            ])
            ->whereHas('course', fn ($q) => $q->where('certificate', true)->where('is_evaluate', false))
            ->whereHas('exam', fn ($q) => $q->where('is_final', true))
            ->where('status', 'success')
            ->when($courseId, fn ($q) => $q->where('course_id', $courseId))
            ->get();
    }

    public function getEvalCertificates(?int $courseId): Collection
    {
        return UserCourseEvaluation::with([
                'course:id,title,title_for_certificate,is_evaluate',
                'user:id,machine_code,name,department_name',
            ])
            ->whereHas('course', fn ($q) => $q->where('certificate', true)->where('is_evaluate', true))
            ->when($courseId, fn ($q) => $q->where('course_id', $courseId))
            ->get()
            ->unique(fn ($item) => $item->user_id . '-' . $item->course_id);
    }
}
