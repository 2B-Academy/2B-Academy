@extends('front.layouts.master')

@section('pageTitle') المحاضرين @endsection


@section('content')


    @include('front.includes.inner-head', ['title' => 'المحاضرين'])



    <!-- ================================ Instructor Section Start ==================================== -->
    <section class="instructor py-60 position-relative z-1">
        <img src="{{asset('front/assets/images/shapes/shape2.png')}}" alt="" class="shape one animation-scalation">
        <img src="{{asset('front/assets/images/shapes/shape6.png')}}" alt="" class="shape six animation-scalation">

        <div class="container">

            <div class="row gy-4">
                @forelse($instructors as $instructor)
                    <div class="col-lg-4 col-sm-6">
                        <div class="instructor-item scale-hover-item bg-white rounded-16 p-12 h-100 border border-neutral-30">
                            <div class="rounded-12 overflow-hidden position-relative bg-neutral-orange">
                                <a href="javascript:void(0)" class="w-100 h-100 d-flex align-items-end">
                                    <img src="{{$instructor->getFileUrl($instructor->image)}}" alt="{{$instructor->name}}"
                                         class="scale-hover-item__img rounded-12 cover-img transition-2">
                                </a>
                            </div>
                            <div class="p-24 position-relative">

                                <div class="">
                                    <h4 class="mb-28 pb-24 border-bottom border-neutral-50 mb-24 border-dashed border-0">
                                        <a href="javascript:void(0)" class="link text-line-2">{{$instructor->name}}</a>
                                    </h4>
                                    <div class="flex-between gap-8 flex-wrap mb-16">
                                        <div class="flex-align gap-8">
                                            <span class="text-neutral-700 text-2xl d-flex"><i class="ph-bold ph-lightbulb"></i></span>
                                            <span class="text-neutral-700 text-lg fw-medium">{{$instructor->job_title}}</span>
                                        </div>
                                        <div class="flex-align gap-8">
                                            <span class="text-neutral-700 text-2xl d-flex"><i class="ph-bold ph-watch"></i></span>
                                            <span class="text-neutral-700 text-lg fw-medium">{{$instructor->courses_count}} دورات</span>
                                        </div>
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
    </section>
    <!-- ================================ Instructor Section End ==================================== -->




    <!-- ================================= Certificate Section Start ================================= -->
    @include('front.includes.before_footer')
    <!-- ================================= Certificate Section End ================================= -->

@endsection
