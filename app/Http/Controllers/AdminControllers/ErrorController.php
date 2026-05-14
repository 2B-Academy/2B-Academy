<?php

namespace App\Http\Controllers\AdminControllers;
use App\Http\Controllers\Controller;
use App\Http\Requests\AboutRequest;
use App\Http\Traits\HasFile;
use App\Models\About;
use App\Models\ErrorLog;
use Illuminate\Http\Request;

class ErrorController extends Controller
{
    use HasFile;


    public function index()
    {
        $content = ErrorLog::latest()->paginate(50);
        return view('admin_dashboard.errors.index',compact('content'));
    }


}
