<?php

namespace App\Http\Controllers\AdminControllers;
use App\Http\Controllers\Controller;
use App\Http\Traits\HasFile;
use App\Models\Course;
use App\Models\UserCourseEvaluation;
use App\Models\UserExam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class UserCertificateController extends Controller
{
    use HasFile;

    public function __construct()
    {
        $this->middleware('permission:users-certificates-index')->only(['index']);
    }

    public function index(Request $request)
    {
        $selectedCourse = $request->course_id;

        $examCertificates = UserExam::with(['course:id,title,is_evaluate', 'user:id,machine_code,name,department_name', 'exam:id,is_final'])
            ->whereHas('course', function ($q) {
                $q->where('certificate', true)->where('is_evaluate', false);
            })
            ->whereHas('exam', function ($q) {
                $q->where('is_final', true);
            })
            ->whereStatus('success')
            ->when($selectedCourse, function ($q) use ($selectedCourse) {
                $q->where('course_id', $selectedCourse);
            })
            ->get();

        $evaluationCertificates = UserCourseEvaluation::with(['course:id,title,is_evaluate', 'user:id,machine_code,name,department_name'])
            ->whereHas('course', function ($q) {
                $q->where('certificate', true)
                    ->where('is_evaluate', true);
            })
            ->when($selectedCourse, function ($q) use ($selectedCourse) {
                $q->where('course_id', $selectedCourse);
            })
            ->get()
            ->unique(function ($item) {
                return $item->user_id . '-' . $item->course_id;
            });

        // 3️⃣ دمج الاثنين
        $content = $examCertificates->merge($evaluationCertificates)->sortByDesc('created_at');
        // pagination يدوي
        $content = new \Illuminate\Pagination\LengthAwarePaginator(
            $content->forPage(request()->page ?? 1, 20),
            $content->count(),
            20,
            request()->page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $allCourses = Course::active()->pluck('title', 'id');

        return view(
            'admin_dashboard.users-certificates.index',
            compact('content', 'allCourses', 'selectedCourse')
        );
    }

    public function showCertificate(Request $request)
    {
        $userId   = $request->user_id;
        $courseId = $request->course_id;

        $course = Course::findOrFail($courseId);

        $certificate = null;

        // 🟢 الحالة 1: الكورس له تقييم
        if ($course->is_evaluate) {

            $certificate = UserCourseEvaluation::with([
                'course:id,title,certificate,title_for_certificate',
                'user:id,name'
            ])
                ->where('user_id', $userId)
                ->where('course_id', $courseId)
                ->first();

        }
        // 🟢 الحالة 2: الكورس ملوش تقييم → امتحان نهائي
        else {

            $certificate = UserExam::with([
                'course:id,title,certificate,title_for_certificate',
                'exam:id,title,degree,is_final',
                'user:id,name'
            ])
                ->where('user_id', $userId)
                ->where('course_id', $courseId)
                ->whereHas('exam', function ($q) {
                    $q->where('is_final', true);
                })
                ->whereStatus('success')
                ->first();
        }

        if (!$certificate) {
            abort(404);
        }

        $course_title = $course->title_for_certificate ?: $course->title;

        $user_certificate = $this->generateCertificate(
            $course_title,
            $certificate->user->name
        );

        return view(
            'admin_dashboard.users-certificates.show',
            compact('certificate', 'user_certificate', 'course')
        );
    }


    public function downloadAll(Request $request)
    {
        $selectedCourse = $request->course_id;

        $examCertificates = UserExam::with(['course:id,title,is_evaluate', 'user:id,machine_code,name,department_name', 'exam:id,is_final'])
            ->whereHas('course', function ($q) {
                $q->where('certificate', true)->where('is_evaluate', false);
            })
            ->whereHas('exam', function ($q) {
                $q->where('is_final', true);
            })
            ->whereStatus('success')
            ->when($selectedCourse, function ($q) use ($selectedCourse) {
                $q->where('course_id', $selectedCourse);
            })
            ->get();

        $evaluationCertificates = UserCourseEvaluation::with(['course:id,title,is_evaluate', 'user:id,machine_code,name,department_name'])
            ->whereHas('course', function ($q) {
                $q->where('certificate', true)
                    ->where('is_evaluate', true);
            })
            ->when($selectedCourse, function ($q) use ($selectedCourse) {
                $q->where('course_id', $selectedCourse);
            })
            ->get()
            ->unique(function ($item) {
                return $item->user_id . '-' . $item->course_id;
            });

        // 3️⃣ دمج الاثنين
        $certificates = $examCertificates->merge($evaluationCertificates)->sortByDesc('created_at');

        if ($certificates->isEmpty()) {
            return back()->with('error', 'لا توجد شهادات متاحة للتحميل.');
        }

        // 2. نحدد اسم ملف ZIP مؤقت
        $zipFileName = 'certificates_' . now()->format('Y_m_d_H_i_s') . '.zip';
        $zipPath = storage_path("app/public/{$zipFileName}");

        // 3. نعمل ZIP جديد
        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {

            foreach ($certificates as $certificate) {
                $course_title = $certificate->course ? ($certificate->course->title_for_certificate ?: $certificate->course->title) : '';
                $user_name = $certificate->user?->name ?? 'unknown';

                // توليد الصورة (نفس الدالة عندك generateCertificate)
                $imageBase64 = $this->generateCertificate($course_title, $user_name);

                // نحول Base64 إلى صورة فعلية مؤقتة
                $imageData = base64_decode($imageBase64);
                $fileName = "{$user_name}_{$course_title}.jpg";
                $tempPath = storage_path("app/public/temp/{$fileName}");

                // نحفظ الصورة مؤقتًا
                Storage::put("public/temp/{$fileName}", $imageData);

                // نضيفها إلى ZIP
                $zip->addFile($tempPath, $fileName);
            }

            $zip->close();
        }

        // نحذف الملفات المؤقتة بعدين (اختياري)
        Storage::deleteDirectory('public/temp');

        // نرجع الملف كتحميل مباشر
        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

}
