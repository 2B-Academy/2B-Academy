@extends('front.layouts.master')

@section('pageTitle') اختباراتي @endsection


@section('content')


    @include('front.includes.inner-head', ['title' => 'اختباراتي'])


    <!-- =========== student dashbord section start ============== -->
    <section class="bg-main-25 py-60 w-100 h-100">
        <div class="container container--lg">
            <div class="d-flex gap-24  z-2 position-relative">
                @include('front.auth.includes.sidebar')
                <div class="w-100">
                    <div class="mb-32">
                        <div class="d-flex align-items-center gap-16 justify-content-between">
                            <h5 class="mb-16">اختباراتي</h5>
                            <button type="button" class="toggle-student-dashbord-button  text-32 d-xl-none d-block">
                                <i class="ph-bold ph-list"></i>
                            </button>
                        </div>

                        <div class="my-24">
                            <div class="overflow-x-auto">
                                <div class="row mx-0">
                                    @forelse($exams as $exam)
                                        <div class="col-md-6">
                                            <div class="exam-result-card d-lg-flex align-items-center justify-content-around bg-white">
                                                <div class="course position-relative">
                                                    <small class="is_final px-5 rounded text-white {{$exam->exam?->is_final ? 'bg-success' : 'bg-warning'}}">{{$exam->exam?->is_final ? 'اختبار نهائي' : 'تدريب'}}</small>
                                                    <h4 class="mt-2">{{$exam->course?->title}}</h4>
                                                    <p>{{$exam->exam?->title}}</p>
                                                    <div class="buttons d-flex mt-30 align-items-center  gap-14">
                                                        <a class="btn btn-sm btn-main" href="{{route('front.auth.exam-answers', $exam)}}"><i class="ph-bold ph-question mx-1"></i> الإجابات</a>
                                                    </div>
                                                </div>
                                                <div class="user-result {{$exam->status == 'success' ? 'success' : 'failed' }}">
                                                    <span class="circle_numbers_result">{{$exam->user_degree}}</span>
                                                    <div class="border my-2"></div>
                                                    <span class="circle_numbers_total"> {{$exam->exam?->degree}}</span>
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
            </div>
        </div>
    </section>
    <!-- =========== student dashbord section end ============== -->



@endsection

@push('js')
@endpush
