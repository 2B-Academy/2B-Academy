@if(isset($settings['why_us']))
    <!-- ================================ About Two Section Start ==================================== -->
    <section class="about-two py-60 position-relative z-1 bg-main-25">
        <div class="position-relative">
            <div class="container">
                <div class="row gy-xl-0 gy-5 flex-wrap-reverse align-items-center">
                    <div class="col-xl-6 pe-xl-5">
                        <div class="about-two__thumb position-relative">
                            <img src="{{asset('front/assets/images/thumbs/about-two-img.png')}}" class="rounded-16 cover-img  wow bounceIn" alt="" data-tilt data-tilt-max="10" data-tilt-speed="500" data-tilt-perspective="5000" data-tilt-full-page-listening>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="about-two-content">
                            {!! $settings['why_us'] !!}

                            <div class="pt-40 border-top border-neutral-50 mt-40 border-dashed border-0">
                                <a href="{{route('front.about')}}" class="btn btn-main rounded-pill flex-align d-inline-flex gap-8">
                                    قراءة المزيد
                                    <i class="ph-bold ph-arrow-up-right d-flex text-lg"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
    <!-- ================================ About Two Section End ==================================== -->
@endif
