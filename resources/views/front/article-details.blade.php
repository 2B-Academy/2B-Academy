@extends('front.layouts.master')

@section('pageTitle') المقالات | {{$article->title}} @endsection


@section('content')


    @include('front.includes.inner-head', ['title' => 'المقالات', 'single' => $article->title])


    <!-- ================================ Blog Details Section Start =================================== -->
    <div class="blog-page-section py-60">
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-8">
                    <div class="bg-main-25 rounded-16 p-12 border border-neutral-30">
                        <div class="rounded-12 overflow-hidden position-relative">
                            <img src="{{$article->getFileUrl($article->image)}}" alt="{{$article->title}}" class="rounded-12 cover-img transition-2">
                            <div class="position-absolute inset-inline-end-0 inset-block-end-0 me-16 mb-16 py-12 px-24 rounded-8 bg-main-two-600 text-white fw-medium">
                                <h3 class="mb-0 text-white fw-medium">{{date('d', strtotime($article->date_publish))}}</h3>
                                {{\Carbon\Carbon::parse($article->date_publish)->translatedFormat('F')}}
                            </div>
                        </div>
                        <div class="pt-32 pb-24 px-16 position-relative">
                            <h2 class="mb-24"> {{$article->title}} </h2>
                            <p class="text-neutral-500 mb-32">
                                {!! $article->description !!}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    @if(count($articles) > 0)
                        <div class="border border-neutral-30 rounded-12 bg-main-25 p-32 bg-main-25 mt-24">
                            <h4 class="mb-16">مقالات مشابهة</h4>
                            <span class="d-block border border-neutral-30 my-24 border-dashed"></span>

                            @foreach($articles as $article)
                                <div class="flex-align gap-16">
                                    <a href="{{route('front.articles.details', [$article->id, $article->slug])}}" class="flex-shrink-0">
                                        <img src="{{$article->getFileUrl($article->image)}}" alt="{{$article->title}}" class="w-80 h-80 rounded-8 object-fit-cover">
                                    </a>
                                    <div class="flex-grow-1">
                                        <h6 class="text-xl mb-10">
                                            <a href="{{route('front.articles.details', [$article->id, $article->slug])}}"
                                               class="hover-text-main-600 text-line-2">{{strLimit($article->title, 50)}} </a>
                                        </h6>
                                        <span class="text-neutral-500">{{$article->date_publish}}</span>
                                    </div>
                                </div>
                                <span class="d-block border border-neutral-30 my-24 border-dashed"></span>
                            @endforeach
                            <a href="{{route('front.articles')}}" class="h6 mb-0 text-main-600 fw-semibold hover-text-decoration-underline">
                                كل المقالات
                                <i class="ph-bold ph-arrow-right"></i>
                            </a>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
    <!-- ================================ Blog Details Section End =================================== -->


    <!-- ================================= Certificate Section Start ================================= -->
    @include('front.includes.before_footer')
    <!-- ================================= Certificate Section End ================================= -->

@endsection
