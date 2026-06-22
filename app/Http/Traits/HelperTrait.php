<?php

namespace App\Http\Traits;

use App\Models\AdminLoginLog;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\CourseSession;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserCourseEvaluation;
use App\Models\UserLectureProgress;
use App\Models\UsersCourse;
use App\Services\HRSystemService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

Trait HelperTrait
{
    public $paginate =   21;

    //Success Response
    public function successResponse($message = '',$data = [],$statusCode = Response::HTTP_OK)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    //Error Response
    public function errorResponse($message = '',$statusCode = Response::HTTP_OK)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
        ],$statusCode);
    }

    public function admin_pages()
    {
        return [
            'users' => ['index', 'show'],
            'abouts' => ['edit'],
            'categories' => ['index', 'create', 'edit', 'delete'],
            'instructors' => ['index', 'create', 'edit', 'delete'],
            'users-courses' => ['index', 'create', 'edit', 'delete'],
            'users-courses-offline' => ['index', 'create', 'edit', 'delete'],
            'courses' => ['index', 'create', 'edit', 'delete'],
            'courses-sections' => ['index'],
            'courses-resources' => ['index'],
            'courses-assignments' => ['index'],
            'courses-lectures' => ['index', 'create', 'edit', 'delete'],
            'courses-sessions' => ['index', 'create', 'edit', 'delete'],
            'courses-exams' => ['index', 'create', 'edit', 'delete'],
            'users-courses-progress' => ['index'],
            'users-courses-rating' => ['index', 'delete'],
            'users-lectures-questions' => ['index', 'edit', 'delete'],
            'users-courses-assignments' => ['index', 'delete'],
            'users-certificates' => ['index'],
            'blogs' => ['index', 'create', 'edit', 'delete'],
            'testimonials' => ['index', 'create', 'edit', 'delete'],
            'contact_form' => ['index', 'show', 'delete'],
            'settings' => ['edit'],
            'public_notifications' => ['index', 'create'],
            'videos' => ['index', 'create'],
            'forms' => ['index', 'create', 'edit', 'delete'],
            'evaluation-categories' => ['index', 'create', 'edit', 'delete'],
            'evaluations' => ['index', 'create', 'edit', 'delete'],
            'evaluations-reports' => ['index'],
            'attendances' => ['index'],
            'admins' => ['index', 'create', 'edit', 'delete'],
            'roles' => ['index', 'create', 'edit', 'delete'],
        ];
    }

    public function pages()
    {
        return [
            [
                'id' => 1,
                'name' => 'Home',
                'value' => 'home',
            ],
            [
                'id' => 2,
                'name' => 'About us',
                'value' => 'about',
            ],
            [
                'id' => 3,
                'name' => 'Features',
                'value' => 'features',
            ],
            [
                'id' => 4,
                'name' => 'Projects',
                'value' => 'projects',
            ],
            [
                'id' => 5,
                'name' => 'Rental',
                'value' => 'rental',
            ],
            [
                'id' => 6,
                'name' => 'News',
                'value' => 'news',
            ],
            [
                'id' => 7,
                'name' => 'Blogs',
                'value' => 'blogs',
            ],
            [
                'id' => 8,
                'name' => 'Reports',
                'value' => 'reports',
            ],
            [
                'id' => 9,
                'name' => 'Contact us',
                'value' => 'contact',
            ],
            [
                'id' => 10,
                'name' => 'Join us',
                'value' => 'join',
            ],
            [
                'id' => 11,
                'name' => 'Register your interest',
                'value' => 'register',
            ],
            [
                'id' => 12,
                'name' => 'Careers',
                'value' => 'careers',
            ],
            [
                'id' => 13,
                'name' => 'Units',
                'value' => 'units',
            ],
        ];
    }



    public function saveAdminLoginLog($admin)
    {
        $user_agent = request()->header('User-Agent');
        AdminLoginLog::create([
            'admin_id' => $admin->id,
            'email' => $admin->email,
            'ip' => request()->ip(),
            'device_type' =>  getDevice($user_agent),
            'logged_at' => date('Y-m-d H:i:s'),
        ]);
    }


    //Check if user enrolled course or not
    public function hasCourse(int $course_id): bool
    {
        if (!Auth::check()) {
            return false;
        }
        $user = Auth::user();
        $course = Course::find($course_id);
        return $user->courses()->where('courses.id', $course_id)->exists() || (isset($course) && $course->for_public);
    }


    //my Groups in offline course
    public function myGroup(int $course_id)
    {
        if (!Auth::check()) {
            return null;
        }
        $user = Auth::user();
        $user_course = UsersCourse::with('group:id,name')
            ->where('course_id',$course_id)
            ->where('user_id', $user->id)
            ->first();
        return $user_course?->group?->id;
    }

    //User Progress
    public function userCourseProgress($courseId, $userId)
    {
        $course = Course::with('lectures')->findOrFail($courseId);

        $totalLectures = $course->lectures->count();
        $completedLectures = UserLectureProgress::where('user_id', $userId)
            ->whereIn('lecture_id', $course->lectures->pluck('id'))
            ->where('completed', true)
            ->count();

        return $totalLectures > 0
            ? round(($completedLectures / $totalLectures) * 100)
            : 0;
    }


    //Generate certificate
    public function generateCertificate($courseName , $username)
    {
        $path =  public_path('front/assets/images/thumbs/certificate-two-img.jpg');
        $img = Image::read($path);
        // Absolute path so the Arabic glyph shaper loads regardless of the
        // current working directory. Under the web SAPI the CWD is public/
        // (so the historic './arabic/Arabic.php' worked), but CLI / queue /
        // scheduler / test runs have the project root as CWD and would
        // otherwise fatal. public_path() resolves to the exact same file.
        require_once public_path('arabic/Arabic.php');
        $obj = new \ArPHP\I18N\Arabic('Glyphs');
        if ($this->isArabic($courseName)) {
            $course_name = $obj->utf8Glyphs($courseName);
        } else {
            $course_name = $courseName;
        }
        if ($this->isArabic($username)) {
            $user_name = $obj->utf8Glyphs($username);
        } else {
            $user_name = $username;
        }
        $fontPath = public_path('front/assets/fonts/Expo_Arabic_Bold.ttf');
        $fontSize = 80;
        $boxWidth = 505;
        $boxHeight = 100;
        $rightOffset = 600;
        $boxX = $rightOffset;
        $boxY = 500;
        $img->drawRectangle($boxX, $boxY, function ($rectangle) use ($boxWidth, $boxHeight) {
            $rectangle->size($boxWidth, $boxHeight);
            $rectangle->border('000000', 1);
        });
        // ✍️ Draw text centered in the box
        $textX = $boxX + ($boxWidth / 2); // center X of the box
        $textY = $boxY + ($boxHeight / 2); // center Y of the box
        $img->text($user_name, (int) $textX, (int) $textY, function ($font) use ($fontPath, $fontSize) {
            $font->filename($fontPath);
            $font->size($fontSize);
            $font->color('000000');
            $font->align('center');
            $font->valign('middle');
        });
        $img->text($course_name, (int) $textX, (int) ($textY + 250), function ($font) use ($fontPath, $fontSize) {
            $font->filename($fontPath);
            $font->size($fontSize);
            $font->color('000000');
            $font->align('center');
            $font->valign('middle');
        });
        return base64_encode((string) $img->toJpeg());
    }


    function isArabic($text)
    {
        // Regex to match Arabic characters
        $arabicRegex = '/[\x{0600}-\x{06FF}\x{0750}-\x{077F}\x{08A0}-\x{08FF}\x{FB50}-\x{FDFF}\x{FE70}-\x{FEFF}]/u';
        return preg_match($arabicRegex, $text);
    }


    public function userCertificates()
    {
        $userId = auth()->id();
        $examCertificates = auth()->user()->exams()->with(['course:id,title,certificate,title_for_certificate,is_evaluate', 'exam:id,title,degree,is_final'])
            ->whereHas('course', function ($query) {
                $query->where('certificate', true);
            })
            ->whereHas('exam', function ($query) {
                $query->where('is_final', true);
            })
            ->whereStatus('success')
            ->get();

        $evaluationCertificates = UserCourseEvaluation::with('course')->where('user_id', $userId)->get();
        $certificates = collect();
        foreach ($examCertificates as $exam) {
            $course = $exam->course;
            if (!$course) continue;
            if (!$course->is_evaluate) {
                $certificates->push($exam);
            }
        }

        foreach ($evaluationCertificates as $evaluation) {
            $course = $evaluation->course;
            if (!$course || !$course->is_evaluate) continue;

            if (!$certificates->contains(function($item) use ($course) {
                return $item->course->id == $course->id;
            })) {
                $certificates->push($evaluation);
            }
        }
        return $certificates;
    }

    public function userCertificate($course)
    {
        $userId = auth()->id();
        $course = \App\Models\Course::findOrFail($course->id);
        $certificate = null;
        if ($course->is_evaluate) {
            $certificate = UserCourseEvaluation::with('course')
                ->where('user_id', $userId)
                ->where('course_id', $course->id)
                ->first();
        } else {
            $certificate = auth()->user()->exams()
                ->with('course', 'exam')
                ->where('course_id', $course->id)
                ->whereStatus('success')
                ->whereHas('exam', function($q) {
                    $q->where('is_final', 1);
                })
                ->first();
        }
        return $certificate;
    }

    public function months()
    {
        return [
            1 => 'يناير',
            2 => 'فبراير',
            3 => 'مارس',
            4 => 'أبريل',
            5 => 'مايو',
            6 => 'يونيو',
            7 => 'يوليو',
            8 => 'أغسطس',
            9 => 'سبتمبر',
            10 => 'أكتوبر',
            11 => 'نوفمبر',
            12 => 'ديسمبر',
        ];
    }

    public function saveAttendance($user,$course, $is_manual = false)
    {
        if (!$user || !$course)
        {
            return $this->errorResponse('هناك خطأ في البيانات');
        }
        $assigned = $user->courses()->where('courses.id', $course->id)->exists();
        if (!$assigned) {
            return $this->errorResponse('هذه الدورة التدريبية غير مخصصة لهذا المستخدم');
        }
        $user_group_id = UsersCourse::where('user_id', $user->id)->where('course_id', $course->id)->value('group_id') ?? 0;
        $sessions_count = $course->sessions()->where('section_id', $user_group_id)->count();
        $user_sessions_count = $sessions_count > 0 ? $sessions_count : 1;
        $user_attendance_count = Attendance::where('user_id', $user->id)->where('course_id', $course->id)->count();
        if ($user_attendance_count >= $user_sessions_count)
        {
            return $this->errorResponse('تم تسجيل حضورك في جميع محاضرات هذه الدورة التدريبية');
        }
        $attendance_hours = $user_sessions_count > 1 ? round($course->hours / $user_sessions_count, 2) : (float) $course->hours;
        DB::table('attendances')->insert([
            'user_id' => $user->id,
            'user_machine_code' => $user->machine_code,
            'user_department' => $user->department_name,
            'course_category_id' => $course->category?->id,
            'course_category_name' => $course->category?->name,
            'course_id' => $course->id,
            'course_name' => $course->title,
            'course_hours' => $course->hours,
            'section_id' => $user_group_id,
            'attendance_hours' => $attendance_hours,
            'is_manual' => $is_manual,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return $this->successResponse('تم تسجيل الحضور بنجاح');
    }

    public function getUsersFromSheet($main_file)
    {
        $array_codes = [];
        $users_codes = Excel::toCollection(null, $main_file);
        foreach ($users_codes[0] as $key => $value)
        {
            $array_codes[] = (string)$value[0];
        }
        return User::whereIn('machine_code', $array_codes)->pluck('id')->toArray();
    }


}
