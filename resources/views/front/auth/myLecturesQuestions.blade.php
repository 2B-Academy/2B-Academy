@extends('front.layouts.master')

@section('pageTitle') أسئلة المحاضرات @endsection


@section('content')


    @include('front.includes.inner-head', ['title' => 'أسئلة المحاضرات'])


    <!-- =========== student dashbord section start ============== -->
    <section class="bg-main-25 py-60 w-100 h-100">
        <div class="container container--lg">
            <div class="d-flex gap-24  z-2 position-relative">
                @include('front.auth.includes.sidebar')
                <div class="w-100">
                    <div class="mb-32">
                        <div class="d-flex align-items-center gap-16 justify-content-between">
                            <h5 class="mb-16">أسئلة المحاضرات</h5>
                            <button type="button" class="toggle-student-dashbord-button  text-32 d-xl-none d-block">
                                <i class="ph-bold ph-list"></i>
                            </button>
                        </div>

                        <div class="my-24">
                            <div class="overflow-x-auto rounded box-shadow-lg">
                                @if(count($questions) > 0)
                                    <table class="table w-100 table-hover table-striped text-center">
                                        <thead>
                                        <tr class="bg-main-25 border-bottom border-neutral-30">
                                            <th class="text-12 fw-medium text-neutral-500 py-16 px-20">الدوره التدريببة - المحاضرة</th>
                                            <th class="text-12 fw-medium text-neutral-500 py-16 px-20">السؤال</th>
                                            <th class="text-12 fw-medium text-neutral-500 py-16 px-20">الإجابة</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($questions as $question)
                                            <tr class="hover-bg-neutral-20 border-bottom transition-03">
                                                <td class="py-28 px-20 shadow-none">
                                                    <span class="fw-normal text-12 text-neutral-500">{{$question->course?->title}} - {{$question->lecture?->title}}</span>
                                                </td>
                                                <td class="py-28 px-20 shadow-none">
                                                    <span class="fw-normal text-12 text-neutral-500">{{$question->question}}</span>
                                                </td>
                                                <td class="py-28 px-20 shadow-none">
                                                    <span class="fw-normal text-12 text-neutral-500 d-flex align-items-center justify-content-center gap-3">
                                                        <i class="ph-bold ph-circle {{$question->answer ? 'text-success' : 'text-danger'}}"></i>
                                                        {{$question->answer ? $question->answer : 'لم يتم الإجابة بعد'}}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    @include('front.includes.noData')
                                @endif
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
