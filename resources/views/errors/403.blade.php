@extends('admin_dashboard.layout.master')
@section('Page_Title') 403 | غير مسموح بالدخول @endsection


@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-8 mx-auto text-center">
                <img src="{{asset('admin_dashboard/assets/images/error/403.png')}}" width="100%">
                <br>
                <h1 class="text-danger mt-4">عفواً ليس لديك تصريح بالدخول</h1>
            </div>
        </div>
    </div>

@endsection
