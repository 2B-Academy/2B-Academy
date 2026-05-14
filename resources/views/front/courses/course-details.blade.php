@extends('front.layouts.master')

@section('pageTitle') الدورات التدريبية | {{$course->title}} @endsection


@section('content')


    @include('front.includes.inner-head', ['title' => 'الدورات التدريبية', 'single' => $course->title])


    <!-- ============================== Course Details Section Start ============================== -->
    <section class="course-details py-60">
        <div class="container">
            <div class="row gy-4">
                <div class="col-xl-8">
                    <!-- horizontal menu Start -->
                    <div class="horizontal-bar d-lg-flex align-items-center justify-content-around gap-10">
                        <a class="tab_link active" href="#" data-target="course_details">تفاصيل الدورة</a>
                        @if(count($course->sections) > 0)
                        <a class="tab_link" href="#" data-target="curriculums">المحاضرات والإختبارات</a>
                        @endif
                        @if(count($course->resources) > 0)
                        <a class="tab_link" href="#" data-target="resources">المصادر</a>
                        @endif
                        @if(count($course->instructors) > 0)
                        <a class="tab_link" href="#" data-target="instructors">المحاضرين</a>
                        @endif
                        <a class="tab_link" href="#" data-target="ratings">التقييمات</a>
                        <a class="tab_link" href="#" data-target="add_review">إضافة تقييم</a>
                    </div>
                    <!-- Details Content Start -->
                    <div class="course-details__content border border-neutral-30 rounded-12 bg-main-25 p-12" id="course_details">
                        <img src="{{$course->getFileUrl($course->image)}}" alt="{{$course->title}}" class="rounded-8 cover-img">
                        <div class="p-20">
                            <h2 class="mt-24 mb-24">{{$course->title}}</h2>
                            <p class="text-neutral-700">{!! $course->description !!}</p>
                        </div>
                    </div>
                    <!-- Details Content End -->

                    <!-- Curriculum Start -->
                    <div class="border border-neutral-30 rounded-12 bg-main-25 p-32 mt-24" id="curriculums">
                        <h5 class="mb-0">المحاضرات والإختبارات</h5>
                        <span class="d-block border border-neutral-30 my-24 border-dashed"></span>
                        <div class="accordion common-accordion style-three" id="accordionExampleTwo">
                            @if($course->course_type == 'offline')
                                @include('front.courses.includes.sessions')
                            @else
                                @include('front.courses.includes.sections')
                            @endif
                        </div>
                    </div>
                    <!-- Curriculum End -->


                    @if(count($course->resources) > 0)
                    <!-- resources Start -->
                    <div class="resources border border-neutral-30 rounded-12 bg-main-25 p-32 mt-24" id="resources">
                        <h5 class="mb-0">المصادر</h5>
                        <span class="d-block border border-neutral-30 my-24 border-dashed"></span>
                        <div class="row m-0">
                            @foreach($course->resources as $resource)
                                <div class="col-md-12">
                                    <div class="resource d-lg-flex align-items-center justify-content-between p-3 mb-6">
                                        <h6 class="mb-0 d-flex align-items-center gap-5"><i class="ph-bold ph-arrow-left mx-2"></i>
                                            {{$resource->title}}</h6>
                                        <a class="btn btn-main download-btn fs-14 p-5 py-8 d-flex align-items-center"
                                           href="{{(\Illuminate\Support\Facades\Auth::check()) ? $resource->link ?? $resource->getFileUrl($resource->file) : route('front.auth.login')}}" target="_blank">
                                            <i class="ph-bold ph-eye mx-2"></i> مشاهدة</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <!-- resources End -->
                    @endif


                    <!-- Instructor Start -->
                    @if(count($course->instructors) > 0)
                        @foreach($course->instructors as $instructor)
                            <div class="border border-neutral-30 rounded-12 bg-main-25 p-32 mt-24" id="instructors">
                                <div class="d-flex align-items-center flex-wrap flex-md-nowrap gap-32">
                                    <img src="{{$instructor->getFileUrl($instructor->image)}}" alt="" class=""
                                         style="width: 150px;border-radius: 50%;    height: 150px;object-fit: cover;">
                                    <div class="">
                                        <div class="flex-between gap-16">
                                            <h4 class="mb-0">{{$instructor->name}}</h4>

                                        </div>
                                        <span class="d-block border border-neutral-30 my-20 border-dashed"></span>
                                        <div class="d-flex flex-column gap-16 flex-wrap max-w-340">
                                            <div class="flex-between gap-8">
                                                <div class="flex-align gap-8">
                                                    <span class="text-neutral-700 text-2xl d-flex"><i class="ph-bold ph-lightbulb"></i></span>
                                                    <span class="text-neutral-700 text-lg fw-medium">{{$instructor->job_title}}</span>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                    <!-- Instructor End -->

                    <!-- Review Start -->
                    <div class="border border-neutral-30 rounded-12 bg-main-25 p-32 mt-24" id="ratings">
                        <h5 class="mb-0">متوسط التقييمات</h5>
                        <span class="d-block border border-neutral-30 my-32 border-dashed"></span>

                        <div class="d-flex flex-sm-row flex-column gap-36">
                            <div class="rounded-16 px-40 py-24 flex-center flex-column flex-shrink-0 text-center bg-main-600 text-white">
                                <h2 class="mb-8 text-white">{{number_format($course->ratings_avg_rating, 2)}}</h2>
                                <div class="flex-center gap-4">
                                    @for($i = 1; $i <= (int)$course->ratings_avg_rating ; $i++)
                                        <span class="text-15 fw-medium text-white d-flex" id="{{$i}}"><i class="ph-fill ph-star"></i></span>
                                    @endfor
                                </div>
                                <span class="mt-8 text-gray-500">{{$course->ratings_count}} تقييم</span>
                            </div>
                        </div>
                        <span class="d-block border border-neutral-30 my-32 border-dashed"></span>
                        <div class="flex-between gap-16 flex-wrap mb-24">
                            <h6 class="mb-0">كل التقييمات</h6>
                        </div>

                        @if(count($course->ratings) > 0)
                            @foreach($course->ratings as $rating)
                                <!-- Review Item -->
                                <div class="border border-neutral-30 rounded-12 bg-white p-32 mt-10">
                                    <div class="flex-align gap-8 mb-16">
                                        @for($i = 1; $i <= $rating->rating ; $i++)
                                            <span class="text-xl fw-medium text-warning-600 d-flex" id="{{$i}}"><i class="ph-fill ph-star"></i></span>
                                        @endfor
                                    </div>
                                    <p class="text-neutral-700">"{{$rating->comment}}"</p>
                                    <span class="d-block border border-neutral-30 my-24 border-dashed"></span>
                                    <div class="flex-align gap-24">
                                        <img src="{{asset('front/assets/images/default-profile.png')}}" alt="" class="w-60 h-60 rounded-circle cover-img">
                                        <div class="">
                                            <h6 class="text-xl mb-8 fw-medium">{{$rating->user?->name}}</h6>
                                        </div>
                                    </div>
                                </div>
                                <!-- Review Item -->
                            @endforeach
                        @endif


                    </div>
                    <!-- Review End -->

                    <!-- Review Form Start -->
                    <div class="border border-neutral-30 rounded-12 bg-main-25 p-32 mt-24" id="add_review">
                        <h5 class="mb-0">اضف تقييمك</h5>
                        <span class="d-block border border-neutral-30 my-32 border-dashed"></span>

                        @auth
                            <form action="{{route('front.course.rating', $course)}}" method="POST" id="ratingForm">
                                @csrf
                                <div class="mb-24">
                                    <label class="d-block text-neutral-700 text-lg fw-medium mb-12">التقييم </label>
                                    <div class="star-rating">
                                        <input type="radio" id="star5" @checked($user_rating?->rating == 5) name="rating" value="5" />
                                        <label class="star" for="star5" title="ممتاز جداً" aria-hidden="true"></label>
                                        <input type="radio" id="star4" @checked($user_rating?->rating == 4) name="rating" value="4" />
                                        <label class="star" for="star4" title="ممتاز" aria-hidden="true"></label>
                                        <input type="radio" id="star3" @checked($user_rating?->rating == 3) name="rating" value="3" />
                                        <label class="star" for="star3" title="جيد جداً" aria-hidden="true"></label>
                                        <input type="radio" id="star2" @checked($user_rating?->rating == 2) name="rating" value="2" />
                                        <label class="star" for="star2" title="جيد" aria-hidden="true"></label>
                                        <input type="radio" id="star1" @checked($user_rating?->rating == 1) name="rating" value="1" />
                                        <label class="star" for="star1" title="سئ" aria-hidden="true"></label>
                                    </div>
                                </div>
                                <div class="mb-24">
                                    <label for="desc" class="text-neutral-700 text-lg fw-medium mb-12">التعليق </label>
                                    <textarea id="desc" class="common-input rounded-24" name="comment" required placeholder="اكتب تعليقك...">{{$user_rating?->comment}}</textarea>
                                </div>
                                <div class="mb-0">
                                    <button type="submit" id="ratingSubmitBtn" class="btn btn-main rounded-pill flex-center gap-8 mt-40">
                                        إرسال
                                        <i class="ph-bold ph-arrow-up-right d-flex text-lg"></i>
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="w-100 alert alert-warning text-center">يمكنك تسجيل الدخول أولاً</div>
                        @endauth
                    </div>
                    <!-- Review Form End -->
                </div>
                <div class="col-xl-4">
                    <div class="course-details__sidebar border border-neutral-30 rounded-12 bg-white p-8">
                        <div class="border border-neutral-30 rounded-12 bg-main-25 p-24 bg-main-25">
                            <span class="text-neutral-700 text-lg mb-12">السعر</span>
                            <div class="flex-align align-items-start flex-wrap gap-8 border-bottom border-neutral-40 pb-24 mb-24">
                                <div class="flex-align gap-12 text-neutral-700">
                                    <span class="text-2xl d-flex"><i class="ph-bold ph-tag"></i></span>
                                    <h2 class="mb-0">{{$course->price > 0 ? $course->price.' '.$course->currency : 'مجاناً'}}</h2>
                                </div>
                                <button type="button" class="text-neutral-500 text-sm" data-bs-toggle="tooltip" data-bs-placement="top"
                                        data-bs-title="{{$course->price > 0 ? $course->price.' '.$course->currency : 'مجاناً'}}">
                                    <i class="ph-bold ph-info"></i>
                                </button>
                            </div>
                            <div class="border-bottom border-neutral-40 pb-24 mb-24 flex-between flex-wrap gap-16">
                                <div class="flex-align gap-12">
                                    <span class="text-neutral-700 text-2xl d-flex"><i class="ph ph-watch"></i></span>
                                    <span class="text-neutral-700 text-lg fw-normal">نوع الدورة</span>
                                </div>
                                <span class="text-lg fw-medium text-neutral-700">{{ $course->course_type == 'offline' ? 'أوفلاين 🔴' : 'أونلاين 🟢' }}</span>
                            </div>
                            <div class="border-bottom border-neutral-40 pb-24 mb-24 flex-between flex-wrap gap-16">
                                <div class="flex-align gap-12">
                                    <span class="text-neutral-700 text-2xl d-flex"><i class="ph ph-watch"></i></span>
                                    <span class="text-neutral-700 text-lg fw-normal">القسم</span>
                                </div>
                                <span class="text-lg fw-medium text-neutral-700">{{$course->category->name}}</span>
                            </div>
                            <div class="border-bottom border-neutral-40 pb-24 mb-24 flex-between flex-wrap gap-16">
                                <div class="flex-align gap-12">
                                    <span class="text-neutral-700 text-2xl d-flex"> <i class="ph ph-video-camera"></i></span>
                                    <span class="text-neutral-700 text-lg fw-normal">عدد السكاشن</span>
                                </div>
                                <span class="text-lg fw-medium text-neutral-700">{{count($course->sections)}}</span>
                            </div>
                            <div class="border-bottom border-neutral-40 pb-24 mb-24 flex-between flex-wrap gap-16">
                                <div class="flex-align gap-12">
                                    <span class="text-neutral-700 text-2xl d-flex"> <i class="ph ph-video-camera"></i></span>
                                    <span class="text-neutral-700 text-lg fw-normal">عدد الفيديوهات</span>
                                </div>
                                <span class="text-lg fw-medium text-neutral-700">{{count($course->lectures)}}</span>
                            </div>
                            <div class="border-bottom border-neutral-40 pb-24 mb-24 flex-between flex-wrap gap-16">
                                <div class="flex-align gap-12">
                                    <span class="text-neutral-700 text-2xl d-flex"><i class="ph ph-globe"></i> </span>
                                    <span class="text-neutral-700 text-lg fw-normal">اللغة</span>
                                </div>
                                <span class="text-lg fw-medium text-neutral-700">{{$course->language}}</span>
                            </div>
                            <div class="border-bottom border-neutral-40 pb-24 mb-24 flex-between flex-wrap gap-16">
                                <div class="flex-align gap-12">
                                    <span class="text-neutral-700 text-2xl d-flex"> <i class="ph ph-chart-pie"></i></span>
                                    <span class="text-neutral-700 text-lg fw-normal">المتسوي</span>
                                </div>
                                <span class="text-lg fw-medium text-neutral-700">{{__('text.'.$course->level)}}</span>
                            </div>
                            <div class="border-bottom border-neutral-40 pb-24 mb-24 flex-between flex-wrap gap-16">
                                <div class="flex-align gap-12">
                                    <span class="text-neutral-700 text-2xl d-flex"> <i class="ph ph-star"></i> </span>
                                    <span class="text-neutral-700 text-lg fw-normal">التقييم</span>
                                </div>
                                <span class="text-lg fw-medium text-neutral-700">{{number_format($course->ratings_avg_rating, 2)}}({{$course->ratings_count}})</span>
                            </div>
                            <div class="border-bottom border-neutral-40 pb-24 mb-24 flex-between flex-wrap gap-16">
                                <div class="flex-align gap-12">
                                    <span class="text-neutral-700 text-2xl d-flex"> <i class="ph ph-question"></i> </span>
                                    <span class="text-neutral-700 text-lg fw-normal">الإختبارات</span>
                                </div>
                                <span class="text-lg fw-medium text-neutral-700">{{count($course->exams)}}</span>
                            </div>

                            <div class="border-bottom border-neutral-40 pb-24 mb-24 flex-between flex-wrap gap-16">
                                <div class="flex-align gap-12">
                                    <span class="text-neutral-700 text-2xl d-flex"> <i class="ph ph-users"></i> </span>
                                    <span class="text-neutral-700 text-lg fw-normal">عدد الموظفين</span>
                                </div>
                                <span class="text-lg fw-medium text-neutral-700">{{$course->for_public ? \App\Models\User::count() : $course->users_count}}</span>
                            </div>
                            <div class=" border-neutral-40 pb-24 mb-24 flex-between flex-wrap gap-16">
                                <div class="flex-align gap-12">
                                    <span class="text-neutral-700 text-2xl d-flex"> <i class="ph ph-certificate"></i> </span>
                                    <span class="text-neutral-700 text-lg fw-normal">الشهادة</span>
                                </div>
                                <span class="text-lg fw-medium text-neutral-700">{{$course->certificate ? 'نعم' : 'لا'}}</span>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ============================== Course Details Section End ============================== -->



@endsection
@push('js')
    <script>
        $(document).on('submit', '#ratingForm', function (e){
           e.preventDefault();
            var url = $(this).attr('action');
            var data = $(this).serialize();
            $.ajax({
                url: url,
                type: 'POST',
                data: data,
                beforeSend:function(){
                    $('#ratingSubmitBtn').attr('disabled', true);
                    $('#ratingSubmitBtn').html('جاري ... <i class="ph-bold ph-arrow-up-right d-flex text-lg"></i>');
                },
                success: function(response) {
                    if(response.status)
                    {
                        swal({
                            title: response.message,
                            text: "شكراً لك.",
                            icon: "success",
                            button: {
                                text: "خروج",
                                value: true,
                                visible: true,
                                closeModal: true
                            }
                        })
                    }
                    else
                    {
                        swal({
                            title: "عفواً",
                            text:  response.message,
                            icon: "error",
                            button: {
                                text: "خروج",
                                value: true,
                                visible: true,
                                closeModal: true
                            }
                        })
                    }
                    $('#ratingSubmitBtn').attr('disabled', false);
                    $('#ratingSubmitBtn').html('إرسال <i class="ph-bold ph-arrow-up-right d-flex text-lg"></i>');
                }
            })
        });


        function userCantAccessLecture(type)
        {
            if(type === 'auth')
            {
                swal({
                    title: 'يجب تسجيل الدخول أولاً',
                    text: "سجل دخول الآن.",
                    icon: "warning",
                    button: {
                        text: "خروج",
                        value: true,
                        visible: true,
                        closeModal: true
                    }
                })
                return;
            }
            else if(type === 'enrolled')
            {
                swal({
                    title: 'أنت ليس مسجل علي هذه الدورة',
                    text: "تواصل مع الإدراة إذا كان لديك مشكلة.",
                    icon: "error",
                    button: {
                        text: "خروج",
                        value: true,
                        visible: true,
                        closeModal: true
                    }
                })
                return;
            }
        }
    </script>
@endpush
