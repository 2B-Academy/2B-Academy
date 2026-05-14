<?php

namespace App\Http\Controllers\AdminControllers;

use App\Exports\CategoryEvaluationsExport;
use App\Exports\EvaluationReportExportByQuestions;
use App\Exports\EvaluationsTextQuestionsExport;
use App\Http\Controllers\Controller;
use App\Http\Traits\HasFile;
use App\Http\Traits\HelperTrait;
use App\Models\Course;
use App\Models\UserCourseEvaluation;
use App\Services\EvaluationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class EvaluationReportController extends Controller
{
    use HasFile, HelperTrait;

    protected $evaluationService;

    public function __construct(EvaluationService $evaluationService)
    {
        $this->middleware('permission:evaluations-reports-index')->only(['index', 'show']);
        $this->evaluationService = $evaluationService;
    }




    /*** Index of the resource.***/
    public function index(Request $request)
    {
        $baseQuery = $this->applyFilters(DB::table('user_course_evaluations'));
        $avgCategoryService = $this->evaluationService->setBaseQuery($baseQuery)->getCategoryEvaluationData();
        $avgPerInstructorCategoryGrouped = $avgCategoryService['categories'];

        //3️⃣ المحاضرين الأعلى تقييمًا
        $topInstructors = $this->applyFilters(DB::table('user_course_evaluations'))
            ->select('instructor_name', DB::raw('AVG(answer / evaluation_type * 5) as avg_rate'))
            ->groupBy('instructor_id', 'instructor_name')
            ->orderByDesc('avg_rate')
            ->limit(5)->get();

         //متوسط كل سؤال
        $data = $this->evaluationService->setBaseQuery($baseQuery)->getEvaluationData();
        $questions = $data['questions'];
        $pivot = $data['pivot'];
        $grandTotal = $data['grandTotal'];
        $results = $data['results'];


        //text questions
        $text_questions = UserCourseEvaluation::where('evaluation_type', 0)
            ->when(request('year'), fn($q) =>
                $q->whereYear('created_at', '=', request('year'))
            )->when(request('month'), fn($q) =>
                $q->whereMonth('created_at', '=', request('month'))
            )->when(request('course_id'), fn($q) =>
                $q->where('course_id', request('course_id'))
            )->get();

        //courses
        $allCourses = Course::active()->pluck('title', 'id');
        return view('admin_dashboard.evaluations-reports.index', compact(
            'data',
            'questions',
            'pivot',
            'grandTotal',
            'results',
            'allCourses',
            'text_questions',
            'topInstructors', 'avgPerInstructorCategoryGrouped'));
    }


    public function applyFilters($query)
    {
        return $query
            ->whereIn('evaluation_type', [5, 10])
            ->when(request('year'), fn($q) =>
            $q->whereYear('created_at', '=', request('year'))
            )
            ->when(request('month'), fn($q) =>
            $q->whereMonth('created_at', '=', request('month'))
            )
            ->when(request('course_id'), fn($q) =>
            $q->where('course_id', request('course_id'))
            );
    }


    public function export_per_questions(Request $request)
    {
        $filters = [
            'course_id' => $request->input('course_id'),
            'month' => $request->input('month'),
            'year' => $request->input('year'),
        ];
        $data = $this->evaluationService->applyFiltersToExport($filters)->getEvaluationData();
        return Excel::download(new EvaluationReportExportByQuestions($data['pivot'], $data['questions'], $data['grandTotal']),
            'evaluations_report' . now()->format('Y-m-d_H-i-s') . '.xlsx'
        );
    }

    public function export_per_category(Request $request)
    {
        $filters = [
            'course_id' => $request->input('course_id'),
            'month' => $request->input('month'),
            'year' => $request->input('year'),
        ];

        $data = $this->evaluationService->applyFiltersToExport($filters)->getCategoryEvaluationData();

        return Excel::download(new CategoryEvaluationsExport($data['categories']),
            'category_evaluations_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
        );
    }


    public function export_per_text(Request $request)
    {
        $data = UserCourseEvaluation::where('evaluation_type', 0)
            ->when(request('year'), fn($q) =>
            $q->whereYear('created_at', '=', request('year'))
            )->when(request('month'), fn($q) =>
            $q->whereMonth('created_at', '=', request('month'))
            )->when(request('course_id'), fn($q) =>
            $q->where('course_id', request('course_id'))
            )->get();

        return Excel::download(new EvaluationsTextQuestionsExport($data),
            'evaluations_text_questions_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
        );
    }

}
