@extends('admin_dashboard.layout.master')
@section('Page_Title')   الآراء | تعديل   @endsection

@section('content')

    <div class="row">
        <div class="col-lg-12 mx-auto">
            <div class="breadcrumb d-flex align-items-center justify-content-between">
                <div class="">
                    <a class="text-dark" href="{{route('admin.testimonials.index')}}">الآراء</a>
                    <span class="mx-2">-</span>
                    <strong class="text-primary">تعديل</strong>
                    <span class="mx-2">-</span>
                    <strong class="text-primary">{{$content->name_ar}}</strong>
                </div>
            </div>
            <div class="card">
                <div class="card-body">

                    <div class="row g-3 mt-4">
                        <div class="col-12">
                            <div class="card shadow-none bg-light border">
                                <div class="card-body">
                                    <form class="row g-3" id="validateForm" method="post" enctype="multipart/form-data"
                                          action="{{route('admin.testimonials.update', $content->id)}}">
                                        @method('put')
                                        @csrf
                                        @include('admin_dashboard.testimonials._form')
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
