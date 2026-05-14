<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Http\Traits\HasFile;
use App\Http\Traits\HelperTrait;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    use HasFile, HelperTrait;

    public function __construct()
    {
        $this->middleware('permission:videos-index')->only(['index', 'show']);
        $this->middleware('permission:videos-create')->only(['create', 'store']);
        $this->middleware('permission:videos-edit')->only(['edit', 'update']);
        $this->middleware('permission:videos-delete')->only(['destroy']);
    }


    /*** Index of the resource.***/
    public function index(Request $request)
    {
        $search = $request->input('search');
        $folderPath = storage_path('app/public/CourseLecture');
        if (!File::exists($folderPath)) {
            File::makeDirectory($folderPath, 0755, true); // 0755 permissions, true = recursive
        }
        $files = File::files($folderPath);
        $filteredFiles = collect($files)->filter(function ($file) use ($search) {
            if (!$search) return true;
            return str_contains(strtolower($file->getFilename()), strtolower($search));
        });
        $content = $filteredFiles->map(function ($file) {
            return [
                'name' => $file->getFilename(),
                'url' => $this->getFileUrl('CourseLecture/' . $file->getFilename()),
            ];
        });
        return view('admin_dashboard.videos.index', compact('content'));
    }

    /*** Create form of the resource.***/
    public function create()
    {
        return view('admin_dashboard.videos.create')->with(['content' => new Course()]);
    }

    /*** Store form of the resource.***/
    public function store(Request $request)
    {
        $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));

        if (!$receiver->isUploaded()) {
            return response()->json(['message' => 'No file uploaded'], 400);
        }

        $save = $receiver->receive();

        if ($save->isFinished()) {
            $file = $save->getFile();

            $allowedMimes = ['video/mp4','video/mpeg','video/ogg','video/webm'];
            if (!in_array($file->getMimeType(), $allowedMimes)) {
                return response()->json(['message' => 'الملف غير مسموح به'], 422);
            }

            $filename = time().'_'.$file->getClientOriginalName();
            $path = $file->storeAs('CourseLecture', $filename, 'public');

            // cleanup temp file
            @unlink($file->getPathname());

            return response()->json([
                'success' => true,
                'path' => $this->getFileUrl($path),
                'message' => 'تم رفع الفيديو بنجاح'
            ]);
        }

        $handler = $save->handler();
        return response()->json([
            'done' => $handler->getPercentageDone()
        ]);
    }

}
