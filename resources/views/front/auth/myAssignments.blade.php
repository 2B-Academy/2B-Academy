@extends('front.layouts.master')

@section('pageTitle') المهام  @endsection


@section('content')


    @include('front.includes.inner-head', ['title' => 'المهام'])


    <!-- =========== student dashbord section start ============== -->
    <section class="bg-main-25 py-60 w-100 h-100">
        <div class="container container--lg">
            <div class="d-flex gap-24  z-2 position-relative">
                @include('front.auth.includes.sidebar')
                <div class="w-100">
                    <div class="mb-32">
                        <div class="d-flex align-items-center gap-16 justify-content-between">
                            <h5 class="mb-16">المهام</h5>
                            <button type="button" class="toggle-student-dashbord-button  text-32 d-xl-none d-block">
                                <i class="ph-bold ph-list"></i>
                            </button>
                        </div>
                        <div class="my-24">
                            <div class="overflow-x-auto">
                                <div class="row mx-0">
                                    <div class="col-12">
                                        @include('errors.validation_error_front')
                                    </div>
                                    @forelse($assignments as $assignment)
                                        <div class="col-md-6">
                                            <div class="exam-result-card px-20 bg-white">
                                                <div class="course position-relative">
                                                    <h5 class="mt-2">{{$assignment['course']}} - {{$assignment['title']}}</h5>
                                                </div>
                                                <div class="mt-3 d-lg-flex align-items-start justify-content-between
                                                   {{is_null($assignment['user_file']) ? 'flex-column' : ''}} gap-3">
                                                    <div class="assignment_file mb-20">
                                                        <a href="{{$assignment['file']}}" class="btn btn-secondary btn-sm" download="">
                                                            <i class="ph ph-download mx-1"></i> ملف المهام
                                                        </a>
                                                    </div>
                                                    <div class="assignment_file">
                                                        @if(!is_null($assignment['user_file']))
                                                            <a href="{{$assignment['user_file']}}" class="btn btn-main btn-sm" download="">
                                                                <i class="ph ph-download mx-1"></i> ملف الموظف
                                                            </a>
                                                        @else
                                                            <form class="form" method="post" enctype="multipart/form-data"
                                                                  action="{{route('front.auth.upload-assignment', $assignment['assignment_id'])}}">
                                                                @csrf
                                                                <div class="d-lg-flex align-items-center gap-3 justify-content-center">
                                                                    <div class="input mb-2">
                                                                        <label>ارفع الملف  (لا يزيد عن 10 ميجا)</label>
                                                                        <input type="file" required class="form-control" name="user_file">
                                                                    </div>
                                                                    <button type="submit" class="btn btn-sm btn-success mt-10">رفع</button>
                                                                </div>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                                @if($assignment['feedback'])
                                                    <hr>
                                                    <div class="feedback mt-3">
                                                        <h6>التقييم:</h6>
                                                        <p>{{$assignment['feedback']}}</p>
                                                    </div>
                                                @endif
                                                @if(!is_null($assignment['score']))
                                                    <hr>
                                                    <div class="feedback mt-3">
                                                        <h6>الدرجة:</h6>
                                                        <strong class="text-main">{{$assignment['score']}} %</strong>
                                                    </div>
                                                @endif
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
