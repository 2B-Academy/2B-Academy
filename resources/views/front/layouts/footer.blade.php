<!-- ==================== Footer Start Here ==================== -->
<footer class="footer bg-neutral-700 position-relative z-1">
    <img src="{{asset('front/assets/images/shapes/shape2.png')}}" alt="" class="shape five animation-scalation">
    <img src="{{asset('front/assets/images/shapes/shape6.png')}}" alt="" class="shape one animation-scalation">

    <div class="py-60 ">
        <div class="container container-two">
            <div class="row gy-5">
                <div class="col-lg-4 col-sm-6 col-xs-6"  data-aos="fade-up" data-aos-duration="400">
                    <div class="footer-item">
                        <h4 class="footer-item__title fw-medium text-white mb-32">الروابط السريعة</h4>
                        <ul class="footer-menu">
                            <li class="mb-16">
                                <a href="{{route('front.about')}}" class="text-white hover-text-main-600 hover-text-decoration-underline">من نحن</a>
                            </li>
                            <li class="mb-16">
                                <a href="{{route('front.courses')}}" class="text-white hover-text-main-600 hover-text-decoration-underline">الدورات التدريببة</a>
                            </li>
                            <li class="mb-16">
                                <a href="{{route('front.instructors')}}" class="text-white hover-text-main-600 hover-text-decoration-underline">المحاضرين</a>
                            </li>
                            <li class="mb-16">
                                <a href="{{route('front.articles')}}" class="text-white hover-text-main-600 hover-text-decoration-underline">المفالات</a>
                            </li>
                            <li class="mb-0">
                                <a href="{{route('front.contact')}}" class="text-white hover-text-main-600 hover-text-decoration-underline">تواصل معنا</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6 col-xs-6"  data-aos="fade-up" data-aos-duration="600">
                    <div class="footer-item">
                        <h4 class="footer-item__title fw-medium text-white mb-32">الأقسام</h4>
                        <ul class="footer-menu">
                            @foreach($front_categories as $category)
                                <li class="mb-16">
                                    <a href="{{route('front.courses')}}?category[]={{$category->id}}" class="text-white hover-text-main-600 hover-text-decoration-underline">
                                        {{$category->name}}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6 col-xs-6"  data-aos="fade-up" data-aos-duration="800">
                    <div class="footer-item">
                        <h4 class="footer-item__title fw-medium text-white mb-32">تواصل معنا</h4>
                        <div class="flex-align gap-20 mb-24">
                            <span class="icon d-flex text-32 text-main-600"><i class="ph ph-phone"></i></span>
                            <div class="">
                                <a href="tel:{{$settings['phone1'] ?? ''}}" class="text-white d-block hover-text-main-600 mb-4">{{$settings['phone1'] ?? ''}}</a>
                                <a href="tel:{{$settings['phone2'] ?? ''}}" class="text-white d-block hover-text-main-600 mb-0">{{$settings['phone2'] ?? ''}}</a>
                            </div>
                        </div>
                        <div class="flex-align gap-20 mb-24">
                            <span class="icon d-flex text-32 text-main-600"><i class="ph ph-envelope-open"></i></span>
                            <div class="">
                                <a href="mailto:{{$settings['email1'] ?? ''}}" class="text-white d-block hover-text-main-600 mb-4">{{$settings['email1'] ?? ''}}</a>
                                <a href="mailto:{{$settings['email2'] ?? ''}}" class="text-white d-block hover-text-main-600 mb-0">{{$settings['email2'] ?? ''}}</a>
                            </div>
                        </div>
                        <div class="flex-align gap-20 mb-0">
                            <span class="icon d-flex text-32 text-main-600"><i class="ph ph-map-trifold"></i></span>
                            <div class="">
                                <span class="text-white d-block mb-4">{{$settings['address1'] ?? ''}}</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="container">
        <!-- bottom Footer -->
        <div class="bottom-footer border-top border-dashed border-neutral-600 border-0 py-32">
            <div class="container container-two">
                <div class="bottom-footer__inner flex-between gap-16 flex-wrap">
                    <div class="footer-item__logo mb-0"  data-aos="zoom-in-right">
                        <a href="{{route('front.home')}}">
                            <img src="{{ getFullPath($settings['footer_logo'] ?? '') ?: asset('front/assets/images/logo/orange_white.png') }}">
                        </a>
                    </div>
                    <p class="text-white text-line-1 fw-normal"  data-aos="zoom-in">    جميع الحقوق محفوظة &copy; {{date('Y')}} <span class="fw-semibold">تو بي</span></p>
                    <ul class="social-list flex-align gap-24"  data-aos="zoom-in-left">
                        @if(isset($settings['facebook']))
                            <li class="social-list__item">
                                <a href="{{$settings['facebook']}}" class="text-white text-2xl hover-text-main-two-600"><i class="ph-bold ph-facebook-logo"></i></a>
                            </li>
                        @endif
                        @if(isset($settings['twitter']))
                            <li class="social-list__item">
                                <a href="{{$settings['twitter']}}" class="text-white text-2xl hover-text-main-two-600"> <i class="ph-bold ph-twitter-logo"></i></a>
                            </li>
                        @endif
                        @if(isset($settings['instagram']))
                            <li class="social-list__item">
                                <a href="{{$settings['instagram']}}" class="text-white text-2xl hover-text-main-two-600"><i class="ph-bold ph-instagram-logo"></i></a>
                            </li>
                        @endif
                        @if(isset($settings['linkedin']))
                            <li class="social-list__item">
                                <a href="{{$settings['linkedin']}}" class="text-white text-2xl hover-text-main-two-600"><i class="ph-bold ph-linkedin-logo"></i></a>
                            </li>
                        @endif
                        @if(isset($settings['youtube']))
                            <li class="social-list__item">
                                <a href="{{$settings['youtube']}}" class="text-white text-2xl hover-text-main-two-600"><i class="ph-bold ph-youtube-logo"></i></a>
                            </li>
                        @endif
                        @if(isset($settings['snapchat']))
                            <li class="social-list__item">
                                <a href="{{$settings['snapchat']}}" class="text-white text-2xl hover-text-main-two-600"><i class="ph-bold ph-snapchat-logo"></i></a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- ==================== Footer End Here ==================== -->
