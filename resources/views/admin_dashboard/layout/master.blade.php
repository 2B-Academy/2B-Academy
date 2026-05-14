<!doctype html>
<html lang="ar" class="semi-dark" dir="rtl">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('front/assets/images/logo/2b_logo.svg')}}" type="image/png" />
    @include('admin_dashboard.layout.css')
    <title> توبي | لوحة التحكم  |  @yield('Page_Title') </title>
    @stack('styles')
</head>

<body class="rtl">

<!--start wrapper-->
<div class="wrapper">

    @include('admin_dashboard.layout.header')

    @include('admin_dashboard.layout.aside')



    <!--start content-->
    <main class="page-content">



        @include('errors.validation_error')

        @yield('content')


    </main>
    <!--end page main-->


    <!--start overlay-->
    <div class="overlay nav-toggle-icon"></div>
    <!--end overlay-->

    <!--Start Back To Top Button-->
    <a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
    <!--End Back To Top Button-->



</div>
<!--end wrapper-->

@include('admin_dashboard.layout.js')

@stack('js')
</body>

</html>
