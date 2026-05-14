<?php

namespace App\Http\Controllers\AdminControllers;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Traits\HasFile;
use App\Jobs\RunSyncEmployees;
use App\Models\Article;
use App\Models\Blog;
use App\Models\Career;
use App\Models\Course;
use App\Models\CourseLectureQuestion;
use App\Models\Industry;
use App\Models\Instructor;
use App\Models\Order;
use App\Models\Service;
use App\Models\Solution;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;


class DashboardController extends Controller
{
    use HasFile;

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function dashboard()
    {
        $statistics = DB::selectOne('
            SELECT
                (SELECT COUNT(*) FROM courses WHERE active=true) AS courses,
                (SELECT COUNT(*) FROM users) AS users,
                (SELECT COUNT(*) FROM instructors) AS instructors,
                (SELECT COUNT(*) FROM articles WHERE active=true) AS articles,
                (SELECT COUNT(*) FROM course_ratings) AS ratings,
                (SELECT COUNT(*) FROM course_lecture_questions) AS lecturesQuestions,
                (SELECT COUNT(*) FROM course_lecture_questions WHERE answer=null) AS lecturesQuestionsNotAnswered,
                (SELECT COUNT(*) FROM user_course_assignments) AS usersAssignments
        ');
        $chart_courses_with_users = Course::active()
           ->withCount('users')
           ->select('id', 'title')
           ->selectRaw('(select count(*) from users_courses where users_courses.course_id = courses.id) as users_count')
           ->orderByDesc('users_count')
           ->limit(10)
           ->get();
        return view('admin_dashboard.dashboard', get_defined_vars());
    }


    public function quickChange(Request $request)
    {
        $item =  app("App\Models\\".$request->item);
        $id = $request->id;
        $val = $request->val;
        $col = $request->col;
        $item::whereId($id)->update([$col=> $val]);
        return response()->json(['success'=>true]);
    }

    public function deleteSelectedItems(Request $request)
    {
        $model =  app("App\Models\\".$request->model);
        $ids = $request->ids;
        $model::whereIn('id',$ids)->delete();
        toastr()->success(__('text.deleteSelectedItems'), ['timeOut' => 8000], 'success');
        return response()->json(['success'=>true]);
    }



    public function userProfile()
    {
        return view('admin_dashboard.userProfile');
    }


    public function updateUserProfile(UpdateProfileRequest $request)
    {
        try {
            $data = $request->validated();
            if(isset($data['password']))
            {
                $data['password'] = Hash::make($data['password']);
                auth()->guard('admin')->user()->update($data);
            }
            else
            {
                auth()->guard('admin')->user()->update(['name'=>$data['name'], 'email' =>$data['email']]);

            }
            toastr()->success(__('text.updateMsg'), ['timeOut' => 8000], 'success');
            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
        }
    }


    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login_page');
    }



    public function uploadTinyFile(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2000',
        ]);
        $path = $this->uploadRequestFile('Tiny',$request, 'file');
        return response()->json([
            'url' => Storage::url($path)
        ]);
    }


    public function videosListFromStorage()
    {
        $files = File::files(storage_path('app/public/CourseLecture'));
        $videos = [];

        foreach ($files as $file) {
            $videos[] = [
                'name' => $file->getFilename(),
                'url' => 'CourseLecture/' . $file->getFilename(),
            ];
        }
        return response()->json($videos);
    }


    public function syncEmployeesJob()
    {
        RunSyncEmployees::dispatch();
        return back()->with('success', 'جاري جلب الموظفين من الأبلكيشن , يمكنك الأطلاع عليهم بعد 5 دقائق من الآن');
    }

}
