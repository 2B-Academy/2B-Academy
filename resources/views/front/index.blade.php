@extends('front.layouts.master')

@section('pageTitle') الرئيسية @endsection


@section('content')

    <!-- ========================= Banner Two Section Start =============================== -->
    <section class="banner-two pt-80 position-relative overflow-hidden bg-banner" style="background-image: url('{{ getFullPath($settings['banner_background'] ?? '') ?: asset('front/assets/images/bg/banner.jpg') }}');">
        <div class="overlay-banner"></div>
        <div class="container">
            <div class="row gy-5 align-items-center">
                <div class="col-xl-12">

                    <div class="banner-content pe-md-4 mt-110">
                        {!! $settings['banner_description'] ?? '' !!}
                        <div class="buttons-wrapper flex-align flex-wrap gap-24 mt-40">
                            <a href="{{route('front.courses')}}" class="btn btn-main rounded-pill flex-align gap-8" data-aos="fade-right">
                                تصفح الدورات
                                <i class="ph-bold ph-arrow-up-right d-flex text-lg"></i>
                            </a>
                            <a href="{{route('front.about')}}" class="btn btn-outline-main rounded-pill flex-align gap-8" data-aos="fade-left">
                                من نحن
                                <i class="ph-bold ph-arrow-up-right d-flex text-lg"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ========================= Banner Two Section End =============================== -->


    @if(count($front_categories) > 0)
    <!-- ============================ Category Section Start ==================================== -->
    <section class="category py-60 position-relative z-1 mash-reverse">
        <div class="container">
            <div class="section-heading text-center">
                <div class="flex-align d-inline-flex gap-8 mb-16 wow bounceInDown">
                    <span class="text-main-600 text-2xl d-flex"><i class="ph-bold ph-book"></i></span>
                    <h5 class="text-main-600 mb-0">الأقسام</h5>
                </div>
                <h2 class="mb-24 wow bounceIn">ارتقِ بتجربة التعلم الخاصة بك</h2>
            </div>
            <div class="category-item-slider">
                @foreach($front_categories as $category)
                    <div class="category-item animation-item h-100 text-center px-16 py-32 rounded-12 bg-main-two-25 border border-neutral-30 hover-border-main-600 transition-2" data-aos="fade-up" data-aos-duration="200">
                    <span class="w-96 h-96 flex-center d-inline-flex bg-white text-main-600 text-40 rounded-circle box-shadow-md mb-24">
                        <a href="{{route('front.courses')}}?category[]={{$category->id}}">
                            <img src="{{$category->getFileUrl($category->logo)}}" class="animate__flipInY" alt="{{$category->name}}">
                        </a>
                    </span>
                        <h4 class="display-four mb-16 text-neutral-700">{{$category->name}}</h4>
                        <a href="{{route('front.courses')}}?category[]={{$category->id}}" class="py-12 px-24 bg-white rounded-8 border border-neutral-30 mt-28 fw-semibold text-main-600 hover-bg-main-600 hover-text-white hover-border-main-600">
                            {{$category->courses_count}} دورة تدريبية
                        </a>
                    </div>
                @endforeach
            </div>
            <div class="flex-align gap-16 mt-40 justify-content-center">
                <button type="button" id="category-prev" class="slick-prev slick-arrow flex-center rounded-circle border border-gray-100 hover-border-main-600 text-xl hover-bg-main-600 hover-text-white transition-1 w-48 h-48">
                    <i class="ph ph-caret-left"></i>
                </button>
                <button type="button" id="category-next" class="slick-next slick-arrow flex-center rounded-circle border border-gray-100 hover-border-main-600 text-xl hover-bg-main-600 hover-text-white transition-1 w-48 h-48">
                    <i class="ph ph-caret-right"></i>
                </button>
            </div>
        </div>
    </section>
    <!-- ============================ Category Section End ==================================== -->
    @endif


    @if(count($courses) > 0)
    <!-- ================================== Explore Course Two Section Start =========================== -->
    <section class="explore-course py-60 bg-main-25 position-relative z-1">
        <img src="{{asset('front/assets/images/shapes/shape2.png')}}" alt="" class="shape six animation-scalation">

        <div class="container">
            <div class="section-heading text-center">
                <div class="flex-align d-inline-flex gap-8 mb-16 wow bounceInDown">
                    <span class="text-main-600 text-2xl d-flex"><i class="ph-bold ph-book"></i></span>
                    <h5 class="text-main-600 mb-0">الدورات التدريبية</h5>
                </div>
                <h2 class="mb-24 wow bounceIn">اختر دورة للبدء</h2>
            </div>

            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-categories" role="tabpanel" aria-labelledby="pills-categories-tab" tabindex="0">
                    <div class="row gy-4">
                        @foreach($courses as $course)
                            <div class="col-lg-4 col-sm-6 wow fadeInUp" data-aos="fade-up" data-aos-duration="200">
                               @include('front.courses.includes.course-card')
                            </div>
                        @endforeach
                    </div>
                    <div class="text-center">
                        <a href="{{route('front.courses')}}" class="btn btn-outline-main rounded-pill flex-align d-inline-flex gap-8 mt-40">
                            جميع الدورات التدريببة
                            <i class="ph-bold ph-arrow-up-right d-flex text-lg"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ================================== Explore Course Two Section End =========================== -->
    @endif


    <!-- ============================== Certificate Section End ===================================== -->
    @include('front.includes.certificate_section')
    <!-- ============================== End Certificate Section End ===================================== -->

    <!-- ============================== Why Us Section End ===================================== -->
    @include('front.includes.why_us_section')
    <!-- ============================== End Why Us Section End ===================================== -->


    <!-- ============================== testimonials Section End ===================================== -->
    @include('front.includes.testimonial_section')
    <!-- ============================== End testimonials Section End ===================================== -->


@endsection
