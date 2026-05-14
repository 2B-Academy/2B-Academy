@extends('front.layouts.master')

@section('pageTitle') المحاضرين @endsection


@section('content')


    @include('front.includes.inner-head', ['title' => 'تواصل معنا'])


    <div class="container">
        <div class="row">
            <div class="col-12">
                @include('errors.validation_error_front')
            </div>
        </div>
    </div>

    <!-- =============================== Contact Section Start ================================== -->
    <section class="contact py-60">
        <div class="container">
            <div class="section-heading text-center">
                <div class="flex-align d-inline-flex gap-8 mb-16">
                    <span class="text-main-600 text-2xl d-flex"><i class="ph-bold ph-book"></i></span>
                    <h5 class="text-main-600 mb-0">اتصل بنا</h5>
                </div>
                <h2 class="mb-24">معلومات التواصل</h2>
            </div>
            <div class="row gy-4">

                <div class="col-xl-4 col-md-6">
                    <div class="contact-item bg-main-25 border border-neutral-30 rounded-12 px-32 py-40 d-flex align-items-start gap-24 hover-bg-main-600 transition-2 hover-border-main-600">
                        <span class="contact-item__icon w-60 h-60 text-32 flex-center rounded-circle bg-main-600 text-white flex-shrink-0">
                            <i class="ph ph-envelope-open"></i>
                        </span>
                        <div class="flex-grow-1">
                            <h4 class="mb-12">البريد الإلكتروني</h4>
                            <p class="text-neutral-500">{{$settings['email1'] ?? ''}}</p>
                            <p class="text-neutral-500">{{$settings['email2'] ?? ''}}</p>
                            <a href="mailto:{{$settings['email1']  ?? $settings['email2'] ?? ''}}" class="text-main-600 fw-semibold text-decoration-underline mt-16">ارسل رسالة</a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6">
                    <div class="contact-item bg-main-25 border border-neutral-30 rounded-12 px-32 py-40 d-flex align-items-start gap-24 hover-bg-main-600 transition-2 hover-border-main-600">
                        <span class="contact-item__icon w-60 h-60 text-32 flex-center rounded-circle bg-main-600 text-white flex-shrink-0">
                            <i class="ph ph-phone-call"></i>
                        </span>
                        <div class="flex-grow-1">
                            <h4 class="mb-12">الهاتف</h4>
                            <p class="text-neutral-500">{{$settings['phone1'] ?? ''}}</p>
                            <p class="text-neutral-500">{{$settings['phone2'] ?? ''}}</p>
                            <a href="tel:{{$settings['phone1']  ?? $settings['phone2'] ?? ''}}" class="text-main-600 fw-semibold text-decoration-underline mt-16">اتصل بنا</a>
                        </div>
                    </div>
                </div>
                @if(isset($settings['whatsapp']))
                    <div class="col-xl-4 col-md-6">
                        <div class="contact-item bg-main-25 border border-neutral-30 rounded-12 px-32 py-40 d-flex align-items-start gap-24 hover-bg-main-600 transition-2 hover-border-main-600">
                        <span class="contact-item__icon w-60 h-60 text-32 flex-center rounded-circle bg-main-600 text-white flex-shrink-0">
                            <i class="ph ph-phone-call"></i>
                        </span>
                            <div class="flex-grow-1">
                                <h4 class="mb-12">الواتساب</h4>
                                <p class="text-neutral-500">{{$settings['whatsapp']}}</p>
                                <a href="https://api.whatsapp.com/send?phone={{$settings['whatsapp']}}" target="_blank" class="text-main-600 fw-semibold text-decoration-underline mt-16">ارسل لنا</a>
                            </div>
                        </div>
                    </div>
                @endif
                @if(isset($settings['address1']))
                    <div class="col-xl-4 col-md-6">
                        <div class="contact-item bg-main-25 border border-neutral-30 rounded-12 px-32 py-40 d-flex align-items-start gap-24 hover-bg-main-600 transition-2 hover-border-main-600">
                        <span class="contact-item__icon w-60 h-60 text-32 flex-center rounded-circle bg-main-600 text-white flex-shrink-0">
                            <i class="ph ph-map-pin-line"></i>
                        </span>
                            <div class="flex-grow-1">
                                <p class="text-neutral-500">{{$settings['address1']}} </p>
                                @if(isset($settings['address_map1']))
                                @endif
                                <a href="{{$settings['address_map1']}}" class="text-main-600 fw-semibold text-decoration-underline mt-16">المكان على الخريطة</a>
                            </div>
                        </div>
                    </div>
                @endif

                @if(isset($settings['address2']))
                    <div class="col-xl-4 col-md-6">
                        <div class="contact-item bg-main-25 border border-neutral-30 rounded-12 px-32 py-40 d-flex align-items-start gap-24 hover-bg-main-600 transition-2 hover-border-main-600">
                            <span class="contact-item__icon w-60 h-60 text-32 flex-center rounded-circle bg-main-600 text-white flex-shrink-0">
                                <i class="ph ph-map-pin-line"></i>
                            </span>
                            <div class="flex-grow-1">
                                <p class="text-neutral-500">{{$settings['address2']}} </p>
                                @if(isset($settings['address_map2']))
                                @endif
                                <a href="{{$settings['address_map2']}}" class="text-main-600 fw-semibold text-decoration-underline mt-16">المكان على الخريطة</a>
                            </div>
                        </div>
                    </div>
                @endif

                @if(isset($settings['address3']))
                    <div class="col-xl-4 col-md-6">
                        <div class="contact-item bg-main-25 border border-neutral-30 rounded-12 px-32 py-40 d-flex align-items-start gap-24 hover-bg-main-600 transition-2 hover-border-main-600">
                            <span class="contact-item__icon w-60 h-60 text-32 flex-center rounded-circle bg-main-600 text-white flex-shrink-0">
                                <i class="ph ph-map-pin-line"></i>
                            </span>
                            <div class="flex-grow-1">
                                <p class="text-neutral-500">{{$settings['address3']}} </p>
                                @if(isset($settings['address_map3']))
                                @endif
                                <a href="{{$settings['address_map3']}}" class="text-main-600 fw-semibold text-decoration-underline mt-16">المكان على الخريطة</a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
    <!-- =============================== Contact Section End ================================== -->

    <!-- ====================== Contact Form Section Start ========================= -->
    <section class="contact-form-section py-50 position-relative z-1">
        <div class="container">
            <div class="row gy-5 align-items-center">
                <div class="col-xl-12 col-lg-12 pe-lg-5">
                    <div class="mb-40 md-xl-5">
                        <div class="flex-align d-inline-flex gap-8 mb-16">
                            <span class="text-main-600 text-2xl d-flex"><i class="ph-bold ph-book"></i></span>
                            <h5 class="text-main-600 mb-0">تواصل معنا</h5>
                        </div>
                        <h2 class="mb-24">اذا كان لديك سؤال أو مشكلة , ارسل لنا الآن</h2>
                    </div>

                </div>
                <div class="col-xl-12 col-lg-12 mt-0">
                    <div class="p-24 pt-0 rounded-12 box-shadow-md">
                        <div class="border border-neutral-30 rounded-8 bg-main-25 p-24">
                            <form action="{{route('front.contact.submit')}}" method="POST" id="contactForm">
                                @csrf
                                <span class="d-block border border-neutral-30 my-24 border-dashed"></span>
                                <div class="mb-24">
                                    <label for="name" class="text-neutral-700 text-lg fw-medium mb-12">الأسم </label>
                                    <input type="text" class="common-input rounded-pill border-transparent focus-border-main-600" required name="name" placeholder="ادخل الأسم...">
                                </div>
                                <div class="mb-24">
                                    <label for="email" class="text-neutral-700 text-lg fw-medium mb-12">البريد الإلكتروني </label>
                                    <input type="email" class="common-input rounded-pill border-transparent focus-border-main-600" required name="email" placeholder="ادخل البريد الإلكتروني...">
                                </div>
                                <div class="mb-24">
                                    <label for="phone" class="text-neutral-700 text-lg fw-medium mb-12">الهاتف </label>
                                    <input type="number" min="0" class="common-input rounded-pill border-transparent focus-border-main-600" required name="mobile" placeholder="01XXXXXXXXX">
                                </div>
                                <div class="mb-24">
                                    <label for="desc" class="text-neutral-700 text-lg fw-medium mb-12">الرسالة</label>
                                    <textarea id="desc" class="common-input rounded-24 border-transparent focus-border-main-600 h-110"
                                              name="message" required placeholder="ادخل الرسالة / الطلب / الشكوي / الإقتراح..."></textarea>
                                </div>

                                <div class="mb-24">
                                    <div class="form-group d-flex justify-content-start">
                                        <div class="g-recaptcha" data-sitekey="{{ env('GOOGLE_RECAPTCHA_SITE_KEY') }}"></div>
                                    </div>
                                </div>
                                <div class="mb-0">
                                    <button type="submit" class="btn btn-main rounded-pill flex-center gap-8 mt-40">
                                        ارسال
                                        <i class="ph-bold ph-arrow-up-right d-flex text-lg"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ====================== Contact Form Section End ========================= -->




    <!-- ================================= Certificate Section Start ================================= -->
    @include('front.includes.before_footer')
    <!-- ================================= Certificate Section End ================================= -->

@endsection

@push('js')
        <script src='https://www.google.com/recaptcha/api.js?hl={{currentLanguage()}}'></script>
@endpush
