@extends('front.layouts.master')

@section('pageTitle') الدورات التدريبية @endsection


@section('content')


    @include('front.includes.inner-head', ['title' => 'الدورات التدريبية'])


    <!-- ============================== Course List View Section Start ============================== -->
    <section class="course-list-view py-60">
        <div class="container">
            <div class="row">
                @include('front.courses.includes.sidebar-filter')
                <div class="col-lg-9">
                    <div class="course-list-wrapper">

                        <div class="row gy-4">
                            @forelse($courses as $course)
                                <div class="col-12">
                                    <div class="course-item bg-main-25 rounded-16 p-12 h-100 border border-neutral-30 list-view">
                                        <div class="course-item__thumb rounded-12 overflow-hidden position-relative">
                                            <span class="course_type_label fw-medium text-neutral-700">{{ $course->course_type == 'offline' ? 'أوفلاين 🔴' : 'أونلاين 🟢' }}</span>
                                            <a href="{{route('front.course.details', [$course->id, $course->slug])}}" class="w-100 h-100">
                                                <img src="{{$course->getFileUrl($course->image)}}" alt="{{$course->title}}" class="course-item__img rounded-12 cover-img transition-2">
                                            </a>
                                            <div class="flex-align gap-8 bg-main-600 rounded-pill px-24 py-12 text-white position-absolute inset-block-start-0 inset-inline-start-0 mt-20 ms-20 z-1">
                                                <span class="text-2xl d-flex"><i class="ph ph-clock"></i></span>
                                                <span class="text-lg fw-medium">{{$course->hours}} ساعة</span>
                                            </div>

                                        </div>
                                        <div class="course-item__content flex-grow-1">
                                            <div class="">
                                                <h4 class="mb-28">
                                                    <a href="{{route('front.course.details', [$course->id, $course->slug])}}"
                                                       class="link text-line-2">{{$course->title}}</a>
                                                </h4>
                                                <div class="flex-between gap-8 flex-wrap mb-16">
                                                    <div class="flex-align gap-8">
                                                        <span class="text-neutral-700 text-2xl d-flex"><i class="ph-bold ph-squares-four"></i></span>
                                                        <span class="text-neutral-700 text-lg fw-medium">
                                                             {{$course->category->name}}
                                                        </span>
                                                    </div>
                                                    <div class="flex-align gap-8">
                                                        <span class="text-neutral-700 text-2xl d-flex"><i class="ph-bold ph-video-camera"></i></span>
                                                        <span class="text-neutral-700 text-lg fw-medium">{{$course->lectures_count}} محاضرة</span>
                                                    </div>
                                                    <div class="flex-align gap-8">
                                                        <span class="text-neutral-700 text-2xl d-flex"><i class="ph-bold ph-chart-bar"></i></span>
                                                        <span class="text-neutral-700 text-lg fw-medium">
                                                            {{ __('text.'.$course->level)}}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="flex-between gap-8 flex-wrap">
                                                    <div class="flex-align gap-4">
                                                        <span class="text-2xl fw-medium text-warning-600 d-flex"><i class="ph-fill ph-star"></i></span>
                                                        <span class="text-lg text-neutral-700">
                                                    {{number_format($course->ratings_avg_rating, 2)}}
                                                    <span class="text-neutral-100">({{$course->ratings_count}})</span>
                                                </span>
                                                    </div>
                                                    @foreach($course->instructors as $instructor)
                                                        <div class="flex-align gap-8">
                                                            <span class="text-neutral-700 text-2xl d-flex">
                                                                <img src="{{$instructor->getFileUrl($instructor->image)}}" alt="{{$instructor->name}}" class="w-32 h-32 object-fit-cover rounded-circle">
                                                            </span>
                                                            <span class="text-neutral-700 text-lg fw-medium">{{$instructor->name}}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div class="flex-between gap-8 pt-24 border-top border-neutral-50 mt-28 border-dashed border-0">
                                                <h4 class="mb-0 text-main-two-600">{{$course->price > 0 ? $course->price.' '.$course->currency : 'مجاناً'}}</h4>
                                                <a href="{{route('front.course.details', [$course->id, $course->slug])}}" class="flex-align gap-8 text-main-600 hover-text-decoration-underline transition-1 fw-semibold" tabindex="0">
                                                    تفاصيل الدورة
                                                    <i class="ph ph-arrow-right"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                @include('front.includes.noData')
                            @endforelse

                            {{$courses->links()}}
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </section>
    <!-- ============================== Course List View Section End ============================== -->


@endsection
