@extends('front.layouts.master')

@section('pageTitle') دوراتي @endsection

@push('css')
    <style>
        .sessions-attendance li
        {
            direction: ltr;
            background: #e6e6e6;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 25px;
        }
        .attendance-label
        {
            background: #ffdada;
            font-size: 12px;
            padding: 13px 20px;
            border-radius: 5px;
        }
        .progress
        {
            background: #e2e2e2;
        }
    </style>
@endpush
@section('content')


    @include('front.includes.inner-head', ['title' => 'دوراتي'])


    <!-- =========== student dashbord section start ============== -->
    <section class="bg-main-25 py-60 w-100 h-100">
        <div class="container container--lg">
            <div class="d-flex gap-24  z-2 position-relative">
                @include('front.auth.includes.sidebar')
                <div class="w-100">
                    <div class="mb-32">
                        <div class="d-flex align-items-center gap-16 justify-content-between">
                            <h5 class="mb-16">دوراتي</h5>
                            <button type="button" class="toggle-student-dashbord-button  text-32 d-xl-none d-block">
                                <i class="ph-bold ph-list"></i>
                            </button>
                        </div>

                        <div class="row gy-4 my-5">
                            @forelse($courses as $course)
                                <div class="col-12 wow fadeInUp" data-aos="fade-up" data-aos-duration="800">
                                    <div class="row course-item bg-white rounded-16 p-12 h-100 box-shadow-md shadow align-items-center">
                                        <div class="col-md-3">
                                            <div class="course-item__thumb rounded-12 overflow-hidden position-relative">
                                                <a href="{{route('front.course.details', [$course->id, $course->slug])}}" class="w-100 h-100">
                                                    <img style="height: 250px;" src="{{$course->getFileUrl($course->image)}}" alt="{{$course->title}}" class="course-item__img rounded-12 cover-img transition-2">
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="course-item__content position-relative">
                                                <div class="">
                                                    <div class="mb-16 flex-align gap-16 flex-wrap">
                                                        <span class="badge bg-secondary">{{$course->category->name}}</span>
                                                        <span class="badge bg-{{$course->course_type =='online' ? 'success' : 'danger'}} mx-2">{{$course->course_type}}</span>
                                                        <span class="badge bg-secondary mx-2">له شهادة : {{$course->certificate ? 'نعم' : 'لا'}}</span>
                                                        <span class="badge bg-secondary mx-2">له تقييم : {{$course->is_evaluate ? 'نعم' : 'لا'}}</span>
                                                    </div>
                                                    <h4 class="mb-28">
                                                        <a href="{{route('front.course.details', [$course->id, $course->slug])}}" class="link text-line-2">{{$course->title}}</a>
                                                    </h4>

                                                    <div class="d-lg-flex align-items-center justify-content-start gap-5">
                                                        <a class="btn btn-dark btn-sm" href="{{route('front.course.details', [$course->id, $course->slug])}}">عرض</a>
                                                        @if($course->is_evaluate)
                                                            @if($course->evaluations()->where('user_id', auth()->id())->exists())
                                                                <button type="button" class="btn btn-outline-main btn-sm">تم تقييم هذه الدورة التدريبية</button>
                                                            @else
                                                                <a class="btn btn-main btn-sm" href="{{route('front.auth.course.evaluation', $course)}}">
                                                                    قيم الآن
                                                                </a>
                                                            @endif
                                                        @endif
                                                        @php
                                                            $groupId = optional($course->usersCourses->first())->group_id;
                                                            $totalSessionsCount = \App\Models\CourseSession::where('section_id', $groupId)->count();
                                                            $totalSessions = $totalSessionsCount > 0 ? $totalSessionsCount : 1;
                                                        @endphp
                                                        @if($course->course_type == 'offline' && $course->allow_attendances)

                                                            @if($course->user_attendances_count != $totalSessions)
                                                                <button type="button" data-url="{{route('front.auth.courses.attendance', $course->id)}}" class="btn btn-success btn-sm attendance-modal">
                                                                    تسجيل الحضور
                                                                    <small class="mx-5">( {{$course->user_attendances_count}} \ {{$totalSessions}} )</small>
                                                                </button>
                                                            @endif
                                                        @endif
                                                    </div>
                                                    @if($course->course_type == 'offline')
                                                        <div class="mt-40">
                                                            <span class="text-dark" style="font-size: 12px">
                                                                نسبة الحضور :
                                                                {{ $totalSessions > 0
                                                                    ? number_format(($course->user_attendances_count / $totalSessions) * 100, 0)
                                                                    : 0
                                                                }}%
                                                            </span>
                                                            <div class="progress">
                                                                <div class="progress-bar progress-bar-striped bg-success" role="progressbar"
                                                                     style="width: {{($course->user_attendances_count/$totalSessions) * 100}}%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                @include('front.includes.noData')
                            @endforelse
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- =========== student dashbord section end ============== -->


@endsection

@push('js')
    <script>
        $(document).on('click', '.attendance-modal', function (){
            var url = $(this).data('url');
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    if(response.status)
                    {
                        swal({
                            title: "تم تسجيل حضورك في المحاضرة بنجاح",
                            text: "شكراً لك",
                            icon: "success",
                            button: {
                                text: "خروج",
                                value: true,
                                visible: true,
                                closeModal: true
                            }
                        })
                        location.reload();
                    }
                    else
                    {
                        swal({
                            title: response.message,
                            text: "حدث خطأ",
                            icon: "error",
                            button: {
                                text: "خروج",
                                value: true,
                                visible: true,
                                closeModal: true
                            }
                        })
                    }

                },
                error: function (response) {
                    swal({
                        title: response.message,
                        text: "حدث خطأ",
                        icon: "error",
                        button: {
                            text: "خروج",
                            value: true,
                            visible: true,
                            closeModal: true
                        }
                    })
                }
            });
        });
    </script>
@endpush
