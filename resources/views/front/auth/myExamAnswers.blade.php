@extends('front.layouts.master')

@section('pageTitle') اختباراتي | الإجابات @endsection


@section('content')


    @include('front.includes.inner-head', ['title' => 'اختباراتي - الإجابات'])


    <!-- =========== student dashbord section start ============== -->
    <section class="bg-main-25 py-60 w-100 h-100">
        <div class="container container--lg">
            <div class="d-flex gap-24  z-2 position-relative">
                @include('front.auth.includes.sidebar')
                <div class="w-100">
                    <div class="mb-32">
                        <div class="d-flex align-items-center gap-16 justify-content-between">
                            <h5 class="mb-16">اختباراتي | الإجابات</h5>
                            <button type="button" class="toggle-student-dashbord-button  text-32 d-xl-none d-block">
                                <i class="ph-bold ph-list"></i>
                            </button>
                        </div>

                        <div class="my-24">
                            <div class="overflow-x-auto">
                                <div class="row mx-0">
                                    <div class="col-md-12 my-10">
                                        <div class="exam-result-card d-lg-flex align-items-center justify-content-between p-20">
                                            <div class="course">
                                                <h4>{{$exam_answers->course?->title}}</h4>
                                                <p>{{$exam_answers->exam?->title}}</p>
                                                <div class="buttons d-flex mt-30 align-items-center  gap-14">
                                                    @if($exam_answers->course?->certificate && $exam_answers->status == 'success' && $exam_answers->exam?->is_final)
                                                        <a class="btn btn-sm btn-dark" href="{{route('front.auth.user-certificate', $exam_answers)}}"><i class="ph-bold ph-certificate mx-1"></i> الشهادة</a>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="user-result {{$exam_answers->status == 'success' ? 'success' : 'failed' }}">
                                                <span class="circle_numbers_result">{{$exam_answers->user_degree}}</span>
                                                <div class="border my-2"></div>
                                                <span class="circle_numbers_total"> {{$exam_answers->exam?->degree}}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 my-10">
                                        @foreach($exam_answers->answers as $index => $question)
                                            <!--Single Question-->
                                            <div class="single-question col-12">
                                                <div class="question">
                                                    <h4> {{$index+1}} - {{$question->question}}</h4>
                                                </div>
                                                <div class="row m-0">
                                                    <div class="col-md-6 mb-20">
                                                        <div class="d-flex align-items-center gap-16">
                                                            <div class="form-check common-check mb-0">
                                                                <input class="form-check-input" type="radio" disabled  checked id="{{$question->id}}">
                                                                <label class="form-check-label fw-normal flex-grow-1" for="{{$question->id}}">{{$question->answer}}</label>
                                                            </div>
                                                            <i class="text-white p-5 rounded ph-bold ph-{{$question->is_correct ? 'check bg-success' : 'x bg-danger'}}"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--Single Question-->
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- =========== student dashbord section end ============== -->



@endsection

@push('js')
@endpush
