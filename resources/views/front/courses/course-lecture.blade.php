@extends('front.layouts.master')

@section('pageTitle') {{$course->title}} | {{$lecture->title}} @endsection


@section('content')


    @include('front.includes.inner-head', ['title' => $course->title.' - ' . $lecture->title])


    <!-- ============================== Course Details Section Start ============================== -->
    <section class="course-details py-60 bg-main-25">
        <div class="container">
            <div class="row gy-4">
                <div class="col-xl-10 mx-auto">

                    <div class="p-20 bg-white mb-10 rounded border">
                        <h2 class="mt-24 mb-24">{{$course->title}}</h2>
                        <h4 class="mt-24 mb-24">{{$lecture->title}}</h4>
                    </div>
                    <!-- Details Content Start -->
                    <div class="course-details__content border border-neutral-30 rounded-12 bg-white p-12">
                        @if(!is_null(is_youtube_video($lecture->video)))
                            <iframe width="100%" height="450" src="https://www.youtube.com/embed/{{ is_youtube_video($lecture->video) }}" frameborder="0" allowfullscreen>
                            </iframe>
                        @else
                            <video id="videoPlayer" class="player" playsinline controls>
                                <source src="{{ $lecture->type == 'upload' ? $lecture->getFileUrl($lecture->video) : $lecture->video }}" type="video/mp4">
                            </video>
                        @endif

                    </div>
                    <!-- Details Content End -->
                    <div class="next-prev my-30 d-flex align-items-center justify-content-between">
                        @if($previous)
                            <a class="btn btn-secondary btn-sm d-flex align-items-center" href="{{ route('front.course.lecture', [$course->id, $previous->id]) }}"><i class="ph-bold ph-arrow-left mx-1"></i> السابق</a>
                        @else
                            <span></span>
                        @endif

                        @if($next)
                            <a class="btn btn-main btn-sm d-flex align-items-center" href="{{ route('front.course.lecture', [$course->id, $next->id]) }}">التالي <i class="ph-bold ph-arrow-right mx-1"></i> </a>
                        @else
                            <span></span>
                        @endif
                    </div>


                    <div class="course-details__sidebar border border-neutral-30 rounded-12 bg-white p-24">
                        <div class="accordion common-accordion style-three" id="accordionExampleTwo">
                            @include('front.courses.includes.sections')
                        </div>
                    </div>

                    <!-- Review Form Start -->
                    <div class="border border-neutral-30 rounded-12 bg-main-25 p-32 mt-24" id="add_review">
                        <form action="{{route('front.course.lecture.addQuestion', [$course, $lecture])}}" id="lectureQuestionForm">
                            @csrf
                            <h5 class="mb-0">اضف سؤال</h5>
                            <span class="d-block border border-neutral-30 my-32 border-dashed"></span>
                            <div class="mb-24">
                                <textarea id="desc" name="question" required class="common-input rounded-24" placeholder="اكتب سؤالك..."></textarea>
                            </div>
                            <div class="mb-0">
                                <button type="submit" id="lectureQuestionBtn" class="btn btn-main rounded-pill flex-center gap-8 mt-40">
                                    إرسال
                                    <i class="ph-bold ph-arrow-up-right d-flex text-lg"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    <!-- Review Form End -->

                </div>

            </div>
        </div>
    </section>
    <!-- ============================== Course Details Section End ============================== -->

@endsection

@push('js')
    <script>
        $(document).on('submit', '#lectureQuestionForm', function (e){
            e.preventDefault();
            var url = $(this).attr('action');
            var data = $(this).serialize();
            $.ajax({
                url: url,
                type: 'POST',
                data: data,
                beforeSend:function(){
                    $('#lectureQuestionBtn').attr('disabled', true);
                    $('#lectureQuestionBtn').html('جاري ... <i class="ph-bold ph-arrow-up-right d-flex text-lg"></i>');
                },
                success: function(response) {
                    if(response.status)
                    {
                        $('#lectureQuestionForm')[0].reset();
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
                    $('#lectureQuestionBtn').attr('disabled', false);
                    $('#lectureQuestionBtn').html('إرسال <i class="ph-bold ph-arrow-up-right d-flex text-lg"></i>');
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


        const player = new Plyr('#videoPlayer');
        // Get saved percentage from backend
        const savedProgress = {{ $progress->progress ?? 0 }};
        player.on('loadedmetadata', () => {
            if (savedProgress > 0) {
                player.currentTime = (savedProgress / 100) * player.duration;
            }
        });
        // Only save if progress increased (prevent overwriting with smaller value)
        let lastSaved = savedProgress;
        player.on('timeupdate', () => {
            const percentage = Math.round((player.currentTime / player.duration) * 100);
            if (percentage >= lastSaved + 5) { // save every +5%
                lastSaved = percentage;
                saveProgress(percentage);
            }
        });

        function saveProgress(percentage) {
            fetch("{{ route('front.course.lecture.progress') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    lecture_id: "{{ $lecture->id }}",
                    progress: percentage
                })
            });
        }
    </script>
@endpush
