<?php

namespace App\Http\Controllers\AdminControllers;
use App\Http\Controllers\Controller;
use App\Http\Traits\HasFile;
use App\Http\Traits\HelperTrait;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    use HasFile,HelperTrait;

    public function __construct()
    {
        $this->middleware('permission:contact_form-index')->only(['index', 'show']);
        $this->middleware('permission:contact_form-delete')->only(['destroy']);
    }

    /*** Index of the resource.***/
    public function index()
    {
        $content = Contact::latest()->paginate(20);
        return view('admin_dashboard.contacts.index',compact('content'));
    }

    /*** Edit form of the resource.***/
    public function show(Contact $contact)
    {
        if(!$contact->is_seen)
        {
            $contact->update(['is_seen' => true]);
        }
        return view('admin_dashboard.contacts.show')->with(['content' => $contact]);
    }

    /*** Delete form of the resource.***/
    public function destroy(Contact $contact)
    {
        $contact->delete();
    }


}
