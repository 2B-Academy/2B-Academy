@extends('front.layouts.master')

@section('pageTitle') شهاداتي @endsection


@section('content')


    @include('front.includes.inner-head', ['title' => 'شهاداتي'])


    <!-- =========== student dashbord section start ============== -->
    <section class="bg-main-25 py-60 w-100 h-100">
        <div class="container container--lg">
            <div class="d-flex gap-24  z-2 position-relative">
                @include('front.auth.includes.sidebar')
                <div class="w-100">
                    <div class="mb-32">
                        <div class="d-flex align-items-center gap-16 justify-content-between">
                            <h5 class="mb-16">شهاداتي</h5>
                            <button type="button" class="toggle-student-dashbord-button  text-32 d-xl-none d-block">
                                <i class="ph-bold ph-list"></i>
                            </button>
                        </div>
                        <div class="my-24">

                            <div class="overflow-x-auto">
                                <div class="row mx-0">
                                    @forelse($certificates as $certificate)
                                        <div class="col-md-6">
                                            <div class="exam-result-card d-lg-flex align-items-center justify-content-around">
                                                <div class="course">
                                                    <h4>{{ $certificate->course?->title }}</h4>
                                                    @if(isset($certificate->exam))
                                                        <p>{{ $certificate->exam?->title }}</p>
                                                    @else
                                                        <p>تم التقييم</p>
                                                    @endif
                                                </div>
                                                <div class="user-result">
                                                    <a class="btn btn-sm btn-dark" href="{{route('front.auth.user-certificate', $certificate->course)}}">
                                                        <i class="ph-bold ph-certificate mx-1"></i> الشهادة
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        @include('front.includes.noData')
                                    @endforelse

                                </div>
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
