@extends('front.layouts.master')

@section('pageTitle') اختبار عام | {{$form->title}} @endsection

@push('css')
    <style>
        .header , footer{ display: none }
        .form-check-input
        {
            border: 1px solid #000000 !important;
        }
    </style>
@endpush

@section('content')


    <!-- ==================== Breadcrumb Start Here ==================== -->
    <section class="breadcrumb py-20 inner-banner position-relative z-1 overflow-hidden mb-0">
        <div class="overlay-banner"></div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="breadcrumb__wrapper">
                        <h2 class="breadcrumb__title mt-10 fw-semibold text-white text-center"> {{$form->title}}</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ==================== Breadcrumb End Here ==================== -->



    <div class="container">
        <div class="row">
            <div class="col-12">
                @include('errors.validation_error_front')
            </div>
        </div>
    </div>

    <!-- ======================  Form Section Start ========================= -->
    <section class="contact-form-section py-50 position-relative z-1">
        <div class="container">
            <div class="row my-10">
                <div class="col-12">
                    @if($user_form->mark <= 0 || $user_form->duration <= 0)
                    <div class="alert alert-danger d-lg-flex align-items-center justify-content-between">
                        <span>تحذير : لا تستخدم أدوات خارجية مساعدة لأن الإختبار مراقب !!</span>
                        <div class="d-lg-flex align-items-center gap-10">
                            عند انتهاء الوقت - لا يمكنك الإختبار مرة أخري :
                            <div class="bg-danger text-white p-10 rounded text-center my-2">
                                @if(now()->greaterThan($user_form->end_at) || $user_form->mark > 0 || $user_form->duration > 0)
                                    <div style="font-size:15px; font-weight:bold;">انتهى الوقت ⏰</div>
                                @else
                                    <div id="timer" style="font-size:15px; font-weight:bold;"></div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="labels d-lg-flex align-items-center justify-content-center gap-50">
                        <div class="bg-success text-white p-10 rounded text-center my-2">
                            <small>  الأسم : {{$user_form->name}}  </small>
                        </div>
                        <div class="bg-success text-white p-10 rounded text-center my-2">
                            <small> كود الموظف : {{$user_form->machine_code}}  </small>
                        </div>
                        <div class="bg-success text-white p-10 rounded text-center my-2">
                            <small> عدد الأسئلة : {{$form->questions_count}} سؤال </small>
                        </div>
                        <div class="bg-success text-white p-10 rounded text-center my-2">
                            <small> مدة الإختبار : {{$form->duration}} دقيقة </small>
                        </div>
                        <div class="bg-success text-white p-10 rounded text-center my-2">
                            <small> درجة الإختبار : {{$form->full_mark}} درجة </small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row gy-5 align-items-center">
                <div class="col-md-10 mx-auto mt-50">
                    <div class="p-24 pt-0 rounded-12 box-shadow-md">
                        <div class="border border-neutral-30 rounded-8 bg-main-25 p-24">
                            @if(now()->greaterThan($user_form->end_at))
                                <div class="alert alert-danger text-center">
                                    <p>انتهي وقت الإختبار</p>
                                </div>
                                <div class="exam-result-card col-md-3 mx-auto">
                                    <div class="user-result py-60 {{ ($user_form->mark >= ($form->full_mark/2)) ? 'success' : 'failed' }}">
                                        <span class="circle_numbers_result">{{$user_form->mark}}</span>
                                        <div class="border my-2"></div>
                                        <span class="circle_numbers_total"> {{$form->full_mark}}</span>
                                    </div>
                                </div>
                            @elseif($user_form->mark > 0 || $user_form->duration > 0)
                                <div class="alert alert-warning text-center">
                                    <p>لقد قمت بإنهاء الأختبار بالفعل</p>
                                </div>
                                <div class="exam-result-card col-md-3 mx-auto">
                                    <div class="user-result py-60 {{ ($user_form->mark >= ($form->full_mark/2)) ? 'success' : 'failed' }}">
                                        <span class="circle_numbers_result">{{$user_form->mark}}</span>
                                        <div class="border my-2"></div>
                                        <span class="circle_numbers_total"> {{$form->full_mark}}</span>
                                    </div>
                                </div>
                            @else
                                <form action="{{route('front.forms.user.saveExam', [$form->uuid])}}" method="POST" id="userForm">
                                    @csrf
                                    <input type="hidden" name="minutes_remaining" id="minutes_remaining" value="">

                                    @foreach($form->questions as $index => $question)
                                        <!--Single Question-->
                                        <div class="single-question col-12">
                                            <div class="question">
                                                <h4> {{$index+1}} - {{$question->question}}</h4>
                                                <input type="hidden" name="questions[{{$index}}][question_id]" value="{{$question->id}}" required>
                                                <input type="hidden" name="questions[{{$index}}][question_title]" value="{{$question->question}}" required>
                                            </div>
                                            <div class="row m-0">
                                                @if($question->type == 'text')
                                                    <textarea class="form-control mb-20" rows="2" cols="2" name="questions[{{$index}}][answer_id]"
                                                              placeholder="اكتب اجابتك هنا ...."></textarea>
                                                @else
                                                    @foreach($question->answers as $answer)
                                                        <div class="col-md-6 mb-20">
                                                            <div class="flex-between gap-16">
                                                                <div class="form-check common-check mb-0">
                                                                    <input class="form-check-input" type="radio" name="questions[{{$index}}][answer_id]" value="{{$answer->id}}" id="{{$question->id}}_{{$answer->id}}">
                                                                    <label class="form-check-label fw-normal flex-grow-1" for="{{$question->id}}_{{$answer->id}}">{{$answer->answer}}</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif

                                            </div>
                                        </div>
                                        <!--Single Question-->
                                    @endforeach

                                    <div class="col-12 text-center">
                                        <button type="submit" class="btn btn-main btn-lg">إنهاء الإختبار</button>
                                    </div>

                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ======================  Form Section End ========================= -->




@endsection

@push('js')
    <script>
        @if(now()->greaterThan($user_form->end_at) || $user_form->mark > 0 || $user_form->duration > 0)
        @else

        document.addEventListener("visibilitychange", function () {
            if (document.hidden) {
                alert("ممنوع الخروج الإختبار تحت المراقبة!");
            }
        });


        let endAt = new Date("{{ $user_form->end_at }}").getTime();
            let timer = setInterval(function() {
                let now = new Date().getTime();
                let distance = endAt - now;
                if (distance <= 0) {
                    clearInterval(timer);
                    document.getElementById("timer").innerHTML = "انتهى الوقت ⏰";
                    // ممكن هنا تعمل submit تلقائي للامتحان مثلاً
                    $('#userForm').submit();
                    return;
                }

                let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                let seconds = Math.floor((distance % (1000 * 60)) / 1000);

                document.getElementById("timer").innerHTML =
                    `${minutes} دقيقة : ${seconds} ثانية`;

                document.getElementById("minutes_remaining").value = minutes;

            }, 100);
        @endif
    </script>
@endpush
