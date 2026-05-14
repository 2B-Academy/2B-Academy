@extends('front.layouts.master')

@section('pageTitle') من نحن @endsection


@section('content')


    @include('front.includes.inner-head', ['title' => 'من نحن'])

    <!-- ============================== Why Us Section End ===================================== -->
    @include('front.includes.why_us_section')
    <!-- ============================== End Why Us Section End ===================================== -->


    <!-- ================================ About Section Start ==================================== -->
    <section class="about py-60 position-relative z-1 mash-bg-main mash-bg-main-two">
        <div class="position-relative">
            <div class="container">
                <div class="row gy-xl-0 gy-5 flex-wrap-reverse align-items-center">
                    <div class="col-xl-6">
                        <div class="about-content">
                            <div class="mb-40">
                                <div class="flex-align gap-8 mb-16 wow bounceInDown">
                                    <span class="w-8 h-8 bg-main-600 rounded-circle"></span>
                                    <h5 class="text-main-600 mb-0 ">من نحن</h5>
                                </div>
                            </div>
                            {!! $about->about !!}
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <img src="{{$about->getFileUrl($about->image)}}" alt="2b" class="about_img" width="100%">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ================================ About Section End ==================================== -->


    <!-- ================================ About Section Start ==================================== -->
    <section class="about py-60 position-relative z-1 mash-bg-main mash-bg-main-two">
        <img src="{{asset('front/assets/images/shapes/shape2.png')}}" alt="" class="shape one animation-scalation">
        <img src="{{asset('front/assets/images/shapes/shape6.png')}}" alt="" class="shape four animation-scalation">

        <div class="position-relative">
            <div class="container">
                <div class="row gy-xl-0 gy-5 flex-wrap-reverse align-items-center">
                    <div class="col-xl-6">
                        <div class="about-thumbs position-relative pe-lg-5">
                            <img src="{{asset('front/assets/images/shapes/shape7.png')}}" alt="" class="shape seven animation-scalation">

                            <div class="offer-message px-24 py-12 rounded-12 bg-main-two-50 fw-medium flex-align d-inline-flex gap-16 border border-neutral-30 animation-upDown">
                                <span class="flex-shrink-0 w-48 h-48 bg-main-two-600 text-white text-2xl flex-center rounded-circle"><i class="ph ph-watch"></i></span>
                                <div>
                                    <h6 class="mb-4">مجاناً</h6>
                                    <span class="text-neutral-500">لكل الدورات</span>
                                </div>
                            </div>
                            <div class="row gy-4">
                                <div class="col-sm-6">
                                    <img src="{{asset('front/assets/images/thumbs/about-img1.png')}}" alt="" class="rounded-12 w-100" data-tilt data-tilt-max="15" data-tilt-speed="500" data-tilt-perspective="5000" data-tilt-full-page-listening>
                                </div>
                                <div class="col-sm-6">
                                    <div class="flex-align gap-24 mb-24">
                                        <div class="bg-main-600 rounded-12 text-center py-24 px-2 w-50-percent" data-aos="fade-right">
                                            <h1 class="mb-0 text-white counter">20+</h1>
                                            <span class="text-white">سنوات الخبرة</span>
                                        </div>
                                        <div class="bg-neutral-700 rounded-12 text-center py-24 px-2 w-50-percent" data-aos="fade-left">
                                            <h1 class="mb-0 text-white counter">900+</h1>
                                            <span class="text-white">موظف</span>
                                        </div>
                                    </div>
                                    <img src="{{asset('front/assets/images/thumbs/about-img2.png')}}" alt="" class="rounded-12 w-100" data-tilt data-tilt-max="20" data-tilt-speed="500" data-tilt-perspective="5000" data-tilt-full-page-listening>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="about-content">
                            <div class="flex-align align-items-start gap-28 mb-32" data-aos="fade-left" data-aos-duration="200">
                                <span class="w-80 h-80 bg-main-25 border border-neutral-30 flex-center rounded-circle flex-shrink-0">
                                    <img src="{{asset('front/assets/images/icons/about-img1.png')}}" alt="">
                                </span>
                                <div class="flex-grow-1">
                                    <h4 class="text-neutral-500 mb-12">مهمتنا</h4>
                                    <p class="text-neutral-500">{!! $about->mission !!}</p>
                                </div>
                            </div>
                            <div class="flex-align align-items-start gap-28 mb-32" data-aos="fade-left" data-aos-duration="400">
                                <span class="w-80 h-80 bg-main-25 border border-neutral-30 flex-center rounded-circle flex-shrink-0">
                                    <img src="{{asset('front/assets/images/icons/about-img2.png')}}" alt="">
                                </span>
                                <div class="flex-grow-1">
                                    <h4 class="text-neutral-500 mb-12">رؤيتنا</h4>
                                    <p class="text-neutral-500"> {!! $about->vision !!} </p>
                                </div>
                            </div>
                            <div class="flex-align align-items-start gap-28 mb-0" data-aos="fade-left" data-aos-duration="400">
                                <span class="w-80 h-80 bg-main-25 border border-neutral-30 flex-center rounded-circle flex-shrink-0">
                                    <img src="{{asset('front/assets/images/icons/about-img2.png')}}" alt="">
                                </span>
                                <div class="flex-grow-1">
                                    <h4 class="text-neutral-500 mb-12">قيمنا</h4>
                                    <p class="text-neutral-500">{!! $about->goals !!} </p>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ================================ About Section End ==================================== -->


    <!-- ============================== Certificate Section End ===================================== -->
    @include('front.includes.certificate_section')
    <!-- ============================== End Certificate Section End ===================================== -->


    <!-- ============================== testimonials Section End ===================================== -->
    @include('front.includes.testimonial_section')
    <!-- ============================== End testimonials Section End ===================================== -->



    <!-- ================================= Certificate Section Start ================================= -->
    @include('front.includes.before_footer')
    <!-- ================================= Certificate Section End ================================= -->

@endsection
