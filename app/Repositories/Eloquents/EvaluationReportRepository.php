<?php

namespace App\Repositories\Eloquents;

use App\Repositories\Contracts\EvaluationReportRepositoryInterface;
use Illuminate\Support\Facades\DB;

class EvaluationReportRepository implements EvaluationReportRepositoryInterface
{
    public function getEvaluationData(array $filters): array
    {
        $query = $this->buildBaseQuery($filters);

        $results = (clone $query)->select(
            'instructor_id',
            'instructor_name',
            'evaluation_id',
            'evaluation_title',
            DB::raw('AVG(answer / evaluation_type * 5) as avg_rate')
        )
            ->groupBy('instructor_id', 'instructor_name', 'evaluation_id', 'evaluation_title')
            ->get();

        $pivot = [];
        foreach ($results as $row) {
            $pivot[$row->instructor_name]['questions'][$row->evaluation_title] = round($row->avg_rate, 2);
        }

        $overall = (clone $query)
            ->select('instructor_name', DB::raw('AVG(answer / evaluation_type * 5) as overall_rate'))
            ->groupBy('instructor_name')
            ->get()
            ->keyBy('instructor_name');

        foreach ($pivot as $instructor => &$data) {
            $data['overall'] = round($overall[$instructor]->overall_rate ?? 0, 2);
        }

        $questions = $results->pluck('evaluation_title')->unique()->values();

        $grandTotalQuestions = (clone $query)
            ->select('evaluation_title', DB::raw('AVG(answer / evaluation_type * 5) as avg_rate'))
            ->groupBy('evaluation_title')
            ->get()
            ->keyBy('evaluation_title');

        $grandTotalOverall = (clone $query)
            ->select(DB::raw('AVG(answer / evaluation_type * 5) as overall_rate'))
            ->value('overall_rate');

        $grandTotal = ['questions' => [], 'overall' => round($grandTotalOverall, 2)];
        foreach ($questions as $question) {
            $grandTotal['questions'][$question] = round($grandTotalQuestions[$question]->avg_rate ?? 0, 2);
        }

        return [
            'pivot'      => $pivot,
            'questions'  => $questions,
            'grandTotal' => $grandTotal,
            'results'    => $results,
        ];
    }

    public function getCategoryEvaluationData(array $filters): array
    {
        $query = $this->buildBaseQuery($filters);

        $avgPerInstructorCategory = (clone $query)
            ->select(
                'evaluation_category_name',
                'instructor_name',
                DB::raw('AVG(answer / evaluation_type * 5) as avg_rate')
            )
            ->groupBy('instructor_id', 'instructor_name', 'evaluation_category_id', 'evaluation_category_name')
            ->orderBy('avg_rate', 'desc')
            ->get();

        $grouped = $avgPerInstructorCategory
            ->groupBy('evaluation_category_name')
            ->map(fn ($items, $categoryName) => [
                'evaluation_category_name' => $categoryName,
                'instructors' => $items->map(fn ($item) => [
                    'instructor_name' => $item->instructor_name,
                    'avg_rate'        => round($item->avg_rate, 2),
                ])->values(),
            ])->values();

        return [
            'categories' => $grouped,
            'raw_data'   => $avgPerInstructorCategory,
        ];
    }

    private function buildBaseQuery(array $filters)
    {
        $query = DB::table('user_course_evaluations');

        if (!empty($filters['course_id'])) {
            $query->where('course_id', $filters['course_id']);
        }
        if (!empty($filters['month'])) {
            $query->whereMonth('created_at', '=', $filters['month']);
        }
        if (!empty($filters['year'])) {
            $query->whereYear('created_at', '=', $filters['year']);
        }

        return $query;
    }
}
