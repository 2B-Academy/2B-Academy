@extends('front.layouts.master')

@section('pageTitle')  {{$course->title}} | {{$exam->title}} @endsection


@section('content')


    @include('front.includes.inner-head', ['title' => $course->title.' - ' . $exam->title])



    <!-- ============================== Course Details Section Start ============================== -->
    <section class="course-exam py-60">
        <div class="container">
            <div class="row gy-4">
                <div class="col-xl-10 mx-auto">
                    <div class="d-lg-flex align-items-center justify-content-between px-30 bg-white mb-30 rounded border">
                        <h4 class="mt-24 mb-24">{{$exam->title}}</h4>
                        <h5 class="mt-24 mb-24">عدد الأسئلة : <span class="text-neutral-orange">{{$exam->questions_count}} </span></h5>
                        <h5 class="mt-24 mb-24">الدرجة النهائية : <span class="text-neutral-orange">{{$exam->degree}} </span></h5>
                    </div>
                    @if($exam->is_final && $user_course_lectures_progress < 90 && $course->course_type != 'offline' && !$course->outside_materials)
                        <section class="result bg-secondary">
                            <h2 class="title text-white text-center mt-30">لا يمكنك تأدية الأختبار النهائي إلا بعد  مشاهدة 90% من المحاضرات </h2>
                        </section>
                    @else
                        @if($already_submitted)
                            <!-- Result-->
                            <section class="result">
                                <div class="circle-result">
                                    <p class="circle_numbers">
                                        <span class="circle_numbers_result">{{$already_submitted->user_degree}}</span>
                                        <span class="circle_numbers_total">من {{$exam->degree}}</span>
                                    </p>
                                </div>
                                <h2 class="title text-white mt-30">{{$already_submitted->status == 'success' ? 'ناجح' : 'غير ناجح'}}</h2>
                            </section>
                        @else
                            <form class="row mb-30 rounded border bg-main-25 m-0 p-40" action="{{route('front.course.exam.submit',[$course, $exam])}}" method="post">
                                @csrf
                                @foreach($exam->questions as $index => $question)
                                    <!--Single Question-->
                                    <div class="single-question col-12">
                                        <div class="question">
                                            <h4> {{$index+1}} - {{$question->question}}</h4>
                                            <input type="hidden" name="questions[{{$index}}][question_id]" value="{{$question->id}}" required>
                                            <input type="hidden" name="questions[{{$index}}][question_title]" value="{{$question->question}}" required>
                                        </div>
                                        <div class="row m-0">
                                            @foreach($question->answers as $answer)
                                                <div class="col-md-6 mb-20">
                                                    <div class="flex-between gap-16">
                                                        <div class="form-check common-check mb-0">
                                                            <input class="form-check-input" type="radio" required name="questions[{{$index}}][answer_id]" value="{{$answer->id}}" id="{{$question->id}}_{{$answer->id}}">
                                                            <label class="form-check-label fw-normal flex-grow-1" for="{{$question->id}}_{{$answer->id}}">{{$answer->answer}}</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <!--Single Question-->
                                @endforeach

                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-main btn-lg">إنهاء الإختبار</button>
                                </div>
                            </form>
                        @endif
                    @endif


                </div>
            </div>
        </div>
    </section>
    <!-- ============================== Course Details Section End ============================== -->


@endsection
