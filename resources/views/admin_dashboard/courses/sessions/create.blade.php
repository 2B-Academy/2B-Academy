@extends('admin_dashboard.layout.master')
@section('Page_Title')   المحاضرات (الأوفلاين) | إنشاء   @endsection

@section('content')

    <div class="row">
        <div class="col-lg-12 mx-auto">
            <div class="breadcrumb d-flex align-items-center justify-content-between">
                <div class="">
                    <a class="text-dark" href="{{route('admin.courses.edit', $course)}}">{{$course->title}}</a>
                    <span class="mx-2">-</span>
                    <a class="text-dark" href="{{route('admin.courses.sessions.index', $course)}}">المحاضرات (الأوفلاين)</a>
                    <span class="mx-2">-</span>
                    <strong class="text-primary">إنشاء</strong>
                </div>
            </div>
            <div class="card">
                <div class="card-body">

                    <div class="row g-3 mt-4">
                        <div class="col-12">
                            <div class="card shadow-none bg-light border">
                                <div class="card-body">
                                    <form class="row g-3" id="validateForm" method="post" enctype="multipart/form-data"
                                          action="{{route('admin.courses.sessions.store', $course)}}">
                                        @csrf
                                        @include('admin_dashboard.courses.sessions._form')
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div><!--end row-->
                </div>
            </div>
        </div>
    </div>
@endsection
