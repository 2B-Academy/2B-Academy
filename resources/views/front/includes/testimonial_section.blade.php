@if(count($testimonials) > 0)
    <!-- ================================= testimonials Two Section Start ========================================= -->
    <section class="testimonials-two py-60 position-relative z-1">
        <div class="container">

            <div class="section-heading text-center">
                <div class="flex-align d-inline-flex gap-8 mb-16 wow bounceInDown">
                    <span class="text-main-600 text-2xl d-flex"><i class="ph-bold ph-book"></i></span>
                    <h5 class="text-main-600 mb-0">آراء الموظفين </h5>
                </div>
                <h2 class="mb-24 wow bounceIn">ماذا يقول موظفيننا</h2>
            </div>

            <div class="testimonials-two-slider">
                @foreach($testimonials as $testimonial)
                    <div class="testimonials-two-item bg-main-25 rounded-12 p-32" data-aos="fade-up" data-aos-duration="400">
                        <ul class="flex-align gap-8 mb-16">
                            <li class="text-warning-600 text-xl d-flex"><i class="ph-fill ph-star"></i></li>
                            <li class="text-warning-600 text-xl d-flex"><i class="ph-fill ph-star"></i></li>
                            <li class="text-warning-600 text-xl d-flex"><i class="ph-fill ph-star"></i></li>
                            <li class="text-warning-600 text-xl d-flex"><i class="ph-fill ph-star"></i></li>
                            <li class="text-warning-600 text-xl d-flex"><i class="ph-fill ph-star"></i></li>
                        </ul>
                        <p class="text-neutral-700 text-xl">{!! $testimonial->description !!}</p>
                        <div class="flex-between gap-24 flex-wrap pt-28 mt-28 border-top border-neutral-50 mt-28 border-dashed border-0">
                            <div class="flex-align gap-24 ">
                                <img src="{{$testimonial->getFileUrl($testimonial->image)}}" alt="" class="w-60 h-60 object-fit-cover rounded-circle">
                                <div class="">
                                    <h5 class="mb-8 fw-medium">{{$testimonial->name}}</h5>
                                </div>
                            </div>
                            <span class="quate text-48 d-flex opacity-25">
                            <img src="{{asset('front/assets/images/icons/quote-icon.png')}}" alt="">
                        </span>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="flex-center gap-16 mt-40">
                <button type="button" id="testimonials-two-prev" class="slick-prev slick-arrow flex-center rounded-circle border border-gray-100 hover-border-main-600 text-xl hover-bg-main-600 hover-text-white transition-1 w-48 h-48">
                    <i class="ph ph-caret-left"></i>
                </button>
                <button type="button" id="testimonials-two-next" class="slick-next slick-arrow flex-center rounded-circle border border-gray-100 hover-border-main-600 text-xl hover-bg-main-600 hover-text-white transition-1 w-48 h-48">
                    <i class="ph ph-caret-right"></i>
                </button>
            </div>
        </div>
    </section>
    <!-- ================================= testimonials Two Section End ========================================= -->
@endif
