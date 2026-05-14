<?php

namespace App\Http\Controllers\AdminControllers;
use App\Http\Controllers\Controller;
use App\Http\Traits\HasFile;
use App\Http\Traits\HelperTrait;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SettingController extends Controller
{
    use HasFile,HelperTrait;

    public function __construct()
    {
        $this->middleware('permission:settings-edit')->only(['index', 'store']);
    }
    public function index(Request $request)
    {
        $content = Setting::query();
        $content = $content->get();
        return view('admin_dashboard.settings.index',compact('content'));
    }

    public function store(Request $request)
    {
        foreach ($request->settings as $key => $value) {
            $setting_row = Setting::where('key', $key)->first();

            if (!is_object($setting_row))
                continue;
            switch ($setting_row->type) {
                case 'file':
                    $setting_row->value = $this->uploadRequestFile('Setting', $request, null, $value);
                    break;
                default:
                    $setting_row->value = $value;
                    break;
            }
            $setting_row->save();
        }
        Artisan::call('cache:clear');
        toastr()->success(__('text.updateMsg'), ['timeOut' => 8000], 'success');
        return redirect()->back();
    }



}
