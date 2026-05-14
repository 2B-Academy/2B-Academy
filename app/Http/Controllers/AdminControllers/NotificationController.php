<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\PublicNotificationRequest;
use App\Http\Traits\HasFile;
use App\Http\Traits\HelperTrait;
use App\Models\PublicNotification;
use App\Models\PublicNotificationUser;
use App\Services\NotificationsApiService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class NotificationController extends Controller
{
    use HasFile, HelperTrait;

    public function __construct()
    {
        $this->middleware('permission:public_notifications-index')->only(['index', 'show']);
        $this->middleware('permission:public_notifications-create')->only(['create', 'store']);
    }


    /*** Index of the resource.***/
    public function index()
    {
        $content = PublicNotification::latest()->paginate(20);
        return view('admin_dashboard.public_notifications.index', compact('content'));
    }

    /*** Create form of the resource.***/
    public function create()
    {
        return view('admin_dashboard.public_notifications.create')->with(['content' => new PublicNotification]);
    }

    /*** Store form of the resource.***/
    public function store(PublicNotificationRequest $request)
    {
        $data = $request->validated();
        $data['for_public'] = isset($data['for_public']);
        $created = PublicNotification::create($data);
        if($created)
        {
            $notification_service = new NotificationsApiService();
            if ($created->for_public)
            {
                $notification_service->sendNotificationsToAllUsers($created->body , $created->body , $created->body, $created->body);
            }
            else
            {
                $users = [];
                if(isset($data['users']) && count($data['users']) > 0)
                {
                    $users = $data['users'];
                }
                elseif($request->hasFile('users_sheet'))
                {
                    $main_file = $request->file('users_sheet');
                    $array_codes = [];
                    $users_codes = Excel::toCollection(null, $main_file);
                    foreach ($users_codes[0] as $key => $value)
                    {
                        $array_codes[] = (string)$value[0];
                    }
                    $users = $array_codes;
                }
                $users = array_unique($users);
                $insertData = [];
                foreach ($users as $user) {
                    $insertData[] = [
                        'public_notification_id' => $created->id,
                        'user_code' => $user,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                PublicNotificationUser::insert($insertData);
                $body  = $created->body;
                $notification_service->sendNotificationsToSelectedUsers($body, $body, $body, $body, $users);
            }
        }
        toastr()->success(__('text.insertMsg'), ['timeOut' => 8000], 'success');
        return redirect()->back();
    }
}
