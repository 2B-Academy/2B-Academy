@extends('front.layouts.master')

@section('pageTitle') تعليقاتي @endsection


@section('content')


    @include('front.includes.inner-head', ['title' => 'تعليقاتي'])


    <!-- =========== student dashbord section start ============== -->
    <section class="bg-main-25 py-60 w-100 h-100">
        <div class="container container--lg">
            <div class="d-flex gap-24  z-2 position-relative">
                @include('front.auth.includes.sidebar')
                <div class="w-100">
                    <div class="mb-32">
                        <div class="d-flex align-items-center gap-16 justify-content-between">
                            <h5 class="mb-16">تعليقاتي</h5>
                            <button type="button" class="toggle-student-dashbord-button  text-32 d-xl-none d-block">
                                <i class="ph-bold ph-list"></i>
                            </button>
                        </div>


                        <div class="row gy-4 my-5">
                            @forelse($ratings as $rating)
                                <div class="col-md-6 mb-4">
                                    <div class="user-rate-box bg-white rounded py-50 px-20 box-shadow-lg">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h4 class="mb-0 text-secondary">{{$rating->course?->title}}</h4>
                                            <div class="flex-center gap-4">
                                                @for($i = 1; $i <= (int)$rating->rating ; $i++)
                                                    <span class="text-15 fw-medium text-main d-flex" id="{{$i}}"><i class="ph-fill ph-star"></i></span>
                                                @endfor
                                            </div>
                                        </div>
                                        <small>{{date('Y-m-d H:i A', strtotime($rating->updated_at))}}</small>
                                        <p class="mt-5 mb-2">{{$rating->comment}}</p>
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
    </section>
    <!-- =========== student dashbord section end ============== -->



@endsection

@push('js')
@endpush
