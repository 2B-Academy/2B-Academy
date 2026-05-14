@extends('front.layouts.master')

@section('pageTitle') ملفي الشخصي @endsection


@section('content')


    @include('front.includes.inner-head', ['title' => 'ملفي الشخصي'])


    <!-- =========== student dashbord section start ============== -->
    <section class="bg-main-25 py-60 w-100 h-100">
        <div class="container container--lg">
            <div class="d-flex gap-24  z-2 position-relative">
                @include('front.auth.includes.sidebar')
                <div class="w-100">
                    <div class="mb-32">
                        <div class="d-flex align-items-center gap-16 justify-content-between">
                            <h5 class="mb-16">صفحتي الشخصية</h5>
                            <button type="button" class="toggle-student-dashbord-button  text-32 d-xl-none d-block">
                                <i class="ph-bold ph-list"></i>
                            </button>
                        </div>
                        @include('front.auth.includes.stats')
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- =========== student dashbord section end ============== -->



@endsection

@push('js')
@endpush
