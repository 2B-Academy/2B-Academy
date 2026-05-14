<?php

namespace App\Http\Controllers\FrontControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use App\Http\Traits\HelperTrait;
use App\Models\About;
use App\Models\Article;
use App\Models\Contact;
use App\Models\Course;
use App\Models\Instructor;
use App\Models\Setting;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CMSController extends Controller
{
    use HelperTrait;


    public function home()
    {
        $courses = Course::with('category')->whereHas('category', function ($query) {
                $query->active();
            })
            ->withCount('lectures')
            ->withCount('ratings')
            ->withAvg('ratings', 'rating')
            ->active()
            ->latest()
            ->limit(3)
            ->get();
        $testimonials = Testimonial::active()->latest()->get();
        return view('front.index', compact('courses','testimonials'));
    }

    public function about()
    {
        $about = About::first() ?? new About();
        $testimonials = Testimonial::active()->latest()->get();
        return view('front.about', compact('about', 'testimonials'));
    }

    public function instructors()
    {
        $instructors = Instructor::withCount('courses')->latest()->get();
        return view('front.instructors', compact('instructors'));
    }

    public function articles()
    {
        $articles = Article::active()->paginate(21);
        return view('front.articles', compact('articles'));
    }

    public function articleDetails($id, $slug)
    {
        $article = Article::active()->findOrFail($id);
        $articles = Article::active()->where('id', '!=', $id)->limit(4)->get();
        return view('front.article-details', compact('article','articles'));
    }

    public function contact()
    {
        return view('front.contact-us');
    }

    public function submitContact(ContactRequest $request)
    {
        $data = $request->validated();
        Contact::create($data);
        Session::flash('success', 'تم إرسال البيانات بنجاح');
        return redirect()->back();
    }

}
