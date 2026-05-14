<!-- Title -->
<title> تو بي | @yield('pageTitle')</title>
<!-- Favicon -->
<link rel="shortcut icon" href="{{asset('front/assets/images/logo/2b_logo.svg')}}">
<!-- Bootstrap -->
<link rel="stylesheet" href="{{asset('front/assets/css/bootstrap.min.css')}}">
<!-- select2 -->
<link rel="stylesheet" href="{{asset('front/assets/css/select2.min.css')}}">
<!-- Slick -->
<link rel="stylesheet" href="{{asset('front/assets/css/slick.css')}}">
<!-- Slick -->
<link rel="stylesheet" href="{{asset('front/assets/css/magnific-popup.css')}}">
<!-- jquery-ui -->
<link rel="stylesheet" href="{{asset('front/assets/css/jquery-ui.css')}}">
<!-- plyr Css -->
<link rel="stylesheet" href="{{asset('front/assets/css/plyr.css')}}">
<!-- Editor js Toolbar Start -->
<link rel="stylesheet" href="{{asset('front/assets/css/editor-quill.css')}}">
<!-- animate -->
<link rel="stylesheet" href="{{asset('front/assets/css/animate.css')}}">
<!-- dataTables.dataTables -->
<link rel="stylesheet" href="{{asset('front/assets/css/dataTables.dataTables.min.css')}}">

<link rel="stylesheet" href="{{asset('front/assets/css/aos.css')}}">
<!-- Main css -->
<link rel="stylesheet" href="{{asset('front/assets/css/main.css')}}">

@stack('css')

<style>
    .course-item__thumb {
        height: 280px;
        background: #f3f3f3;
        padding: 20px;
    }
    .course_type_label
    {
        position: absolute;
        background: #000000;
        left: 0;
        top: 0;
        padding: 3px 20px;
        color: #fff !important;
    }
</style>
