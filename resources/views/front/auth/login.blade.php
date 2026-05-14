@extends('front.layouts.master')

@section('pageTitle') تسجيل الدخول @endsection


@section('content')


    @include('front.includes.inner-head', ['title' => 'تسجيل الدخول'])



    <!-- ============================== Login Section Start ============================== -->
    <div class="account py-60 position-relative">
        <div class="container">
            <div class="row gy-4 align-items-center">
                <div class="col-lg-6">
                    @include('errors.validation_error_front')
                    <div class="bg-main-25 border border-neutral-30 rounded-8 p-32">
                        <div class="mb-40">
                            <h3 class="mb-16 text-neutral-500">مرحباً بك !</h3>
                            <p class="text-neutral-500">سجل دخول الآن</p>
                        </div>
                        <form action="{{route('front.auth.postLogin')}}" method="POST">
                            @csrf
                            <div class="mb-24">
                                <label for="email" class="fw-medium text-lg text-neutral-500 mb-16">الكود الوظيفي</label>
                                <input type="text" required class="common-input rounded-pill" name="email" id="email" placeholder="ادخل الكود الوظيفي...">
                            </div>
                            <div class="mb-16">
                                <label for="password" class="fw-medium text-lg text-neutral-500 mb-16">كلمة المرور</label>
                                <div class="position-relative">
                                    <input type="password" required class="common-input rounded-pill pe-44"  name="password" id="password" placeholder="ادخل كلمة المرور...">
                                </div>
                            </div>
                            <div class="mt-40">
                                <button type="submit" class="btn btn-main rounded-pill flex-center gap-8 mt-40">
                                    تسجيل الدخول
                                    <i class="ph-bold ph-arrow-up-right d-flex text-lg"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-6 d-lg-block d-none">
                    <div class="account-img">
                        <img src="{{asset('front/assets/images/thumbs/account-img.png')}}" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ============================== Login Section End ============================== -->



@endsection

@push('js')
@endpush
