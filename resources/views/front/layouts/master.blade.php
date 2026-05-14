<!DOCTYPE html>
<html lang="ar"  dir="rtl">

<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<head>
    @include('front.layouts.css')
</head>


<body class="light2">

    <!--==================== Preloader Start ====================-->
    <div class="preloader"><div class="loader"></div></div>
    <!--==================== Preloader End ====================-->
    <!--==================== Overlay Start ====================-->
    <div class="overlay"></div>
    <!--==================== Overlay End ====================-->

    <!--==================== Sidebar Overlay End ====================-->
    <div class="side-overlay"></div>
    <!--==================== Sidebar Overlay End ====================-->

    <!-- ==================== Scroll to Top End Here ==================== -->
    <div class="progress-wrap">
        <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
        </svg>
    </div>
    <!-- ==================== Scroll to Top End Here ==================== -->

    <!-- Start Header-->
    @include('front.layouts.header')
    <!-- End Header-->


    <!-- Start Content-->
    @yield('content')
    <!-- End Content-->


    <!-- Start Footer -->
    @include('front.layouts.footer')
    <!-- End Footer-->


    <!-- Start js -->
    @include('front.layouts.js')
    <!-- End js-->

</body>
</html>

