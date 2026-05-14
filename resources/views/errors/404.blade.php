@extends('front.layouts.master')
@section('Page_Title') 404 Page Not Found @endsection


@section('content')

    <div class="container my-5 py-5">
        <div class="row">
            <div class="col-md-8 mx-auto text-center">
                <img src="{{asset('admin_dashboard/assets/images/error/404-error.png')}}" width="100%">
                <br>
                <h1 class="text-danger mt-4">404 صفحة غير موجودة</h1>
            </div>
        </div>
    </div>

@endsection
