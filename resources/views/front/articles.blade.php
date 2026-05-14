@extends('front.layouts.master')

@section('pageTitle') المقالات @endsection


@section('content')


    @include('front.includes.inner-head', ['title' => 'المقالات'])


    <!-- ================================ Blog Page Section Start =================================== -->
    <div class="blog-page-section py-60">
        <div class="container">

            <div class="row gy-4">
                @forelse($articles as $article)
                    <div class="col-lg-4 col-sm-6">
                        <div class="scale-hover-item bg-main-25 rounded-16 p-12 h-100 border border-neutral-30">
                            <div class="course-item__thumb rounded-12 overflow-hidden position-relative">
                                <a href="{{route('front.articles.details', [$article->id, $article->slug])}}" class="w-100 h-100">
                                    <img src="{{$article->getFileUrl($article->image)}}" alt="{{$article->title}}"
                                         class="scale-hover-item__img rounded-12 cover-img transition-2">
                                </a>
                                <div class="position-absolute inset-inline-end-0 inset-block-end-0 me-16 mb-16 py-12 px-24 rounded-8 bg-main-three-600 text-white fw-medium">
                                    <h3 class="mb-0 text-white fw-medium">{{date('d', strtotime($article->date_publish))}}</h3>
                                    {{\Carbon\Carbon::parse($article->date_publish)->translatedFormat('F')}}
                                </div>
                            </div>
                            <div class="pt-32 pb-24 px-16 position-relative">
                                <h4 class="mb-28">
                                    <a href="{{route('front.articles.details', [$article->id, $article->slug])}}" class="link text-line-2">{{$article->title}}</a>
                                </h4>

                                <div class="flex-between gap-8 pt-24 border-top border-neutral-50 mt-28 border-dashed border-0">
                                    <a href="{{route('front.articles.details', [$article->id, $article->slug])}}" class="flex-align gap-8 text-main-600 hover-text-decoration-underline transition-1 fw-semibold" tabindex="0">
                                        اقرأ المزيد
                                        <i class="ph ph-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    @include('front.includes.noData')
                @endforelse
            </div>

            {{$articles->links()}}
        </div>
    </div>
    <!-- ================================ Blog Page Section End =================================== -->

    <!-- ================================= Certificate Section Start ================================= -->
    @include('front.includes.before_footer')
    <!-- ================================= Certificate Section End ================================= -->

@endsection
