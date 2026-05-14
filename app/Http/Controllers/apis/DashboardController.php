<?php

namespace App\Http\Controllers\apis;

use App\Models\Article;
use App\Models\Course;
use App\Models\Instructor;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends ApiController
{
    public function index(): JsonResponse
    {
        $statistics = DB::selectOne('
            SELECT
                (SELECT COUNT(*) FROM courses WHERE active = 1)                          AS courses,
                (SELECT COUNT(*) FROM users)                                              AS users,
                (SELECT COUNT(*) FROM instructors)                                        AS instructors,
                (SELECT COUNT(*) FROM articles WHERE active = 1)                         AS articles,
                (SELECT COUNT(*) FROM course_ratings)                                    AS ratings,
                (SELECT COUNT(*) FROM course_lecture_questions)                          AS lecture_questions,
                (SELECT COUNT(*) FROM course_lecture_questions WHERE answer IS NULL)     AS unanswered_questions,
                (SELECT COUNT(*) FROM user_course_assignments)                           AS user_assignments
        ');

        $topCourses = Course::active()
            ->select('id', 'title')
            ->selectRaw('(SELECT COUNT(*) FROM users_courses WHERE users_courses.course_id = courses.id) AS users_count')
            ->orderByDesc('users_count')
            ->limit(10)
            ->get()
            ->map(fn ($c) => [
                'id'          => $c->id,
                'title'       => $c->getTranslations('title'),
                'users_count' => $c->users_count,
            ]);

        return $this->success(__('messages.retrieved'), [
            'statistics'  => (array) $statistics,
            'top_courses' => $topCourses,
        ]);
    }
}
