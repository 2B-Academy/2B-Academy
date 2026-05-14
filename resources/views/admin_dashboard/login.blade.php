<!doctype html>
<html lang="ar" dir="rtl">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{asset('admin_dashboard/assets/images/2b_logo.svg')}}" type="image/png" />
    <!-- Bootstrap CSS -->
    <link href="{{asset('admin_dashboard/assets/css/bootstrap.min.css')}}" rel="stylesheet" />
    <link href="{{asset('admin_dashboard/assets/css/bootstrap-extended.css')}}" rel="stylesheet" />
    <link href="{{asset('admin_dashboard/assets/css/style.css')}}" rel="stylesheet" />
    <link href="{{asset('admin_dashboard/assets/css/icons.css')}}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Baloo+Bhaijaan+2:wght@400..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">

    <!-- loader-->
    <link href="{{asset('admin_dashboard/assets/css/pace.min.css')}}" rel="stylesheet" />

    <title> @lang('text.websiteName') | @lang('text.dashboard') |  @lang('text.login') </title>
</head>

<body>

<!--start wrapper-->
<div class="wrapper">

    <!--start content-->
    <main class="authentication-content containerForm">
        <div class="container-fluid">
            <div class="authentication-card">
                <div class="card shadow rounded-0 overflow-hidden">
                    <div class="row g-0">
                        <div class="col-lg-6 bg-login d-flex align-items-center justify-content-center">
                            <svg width="100" height="70" viewBox="0 0 86 51" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_88_4786)">
                                    <g clip-path="url(#clip1_88_4786)">
                                        <path d="M40.5829 8.30518V28.0012H9.06038V31.574H40.5829V39.6344H1V19.9383H32.5225V16.3655H1V8.30518H40.5829Z" fill="white"></path>
                                        <path d="M76.7086 8.30518H43.5742V39.6317H79.1441L82.897 35.5985V14.4996L76.7086 8.30518ZM71.1373 16.3655V19.9383H51.6355V16.3655H71.1373ZM75.0203 31.5705H51.6416V28.0013H75.0203V31.5705Z" fill="#F37021"></path>
                                        <path d="M72.3898 45.3326H0.722656V41.6965H77.011L72.3898 45.3326Z" fill="white"></path>
                                    </g>
                                </g>
                                <defs>
                                    <clipPath id="clip0_88_4786">
                                        <rect width="86" height="50.0971" fill="white"></rect>
                                    </clipPath>
                                    <clipPath id="clip1_88_4786">
                                        <rect width="160.629" height="99.4144" fill="white" transform="translate(-38.4082 -22.8765)"></rect>
                                    </clipPath>
                                </defs>
                            </svg>
                        </div>
                        <div class="col-lg-6">
                            <div class="card-body p-4 p-sm-5">
                                <h5 class="card-title mb-3"> @lang('text.websiteName') |  @lang('text.dashboard') |  @lang('text.login')</h5>
                                @include('errors.validation_error')
                                <form class="form-body" method="post" action="{{route('admin.login')}}">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label for="inputEmailAddress" class="form-label"> @lang('text.Email')</label>
                                            <div class="ms-auto position-relative">
                                                <div class="position-absolute top-50 translate-middle-y search-icon px-3"><i class="bi bi-envelope-fill"></i></div>
                                                <input type="email" class="form-control radius-30 ps-5" required name="email" id="inputEmailAddress" placeholder=" @lang('text.Email')">
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label for="inputChoosePassword" class="form-label"> @lang('text.Password')</label>
                                            <div class="ms-auto position-relative">
                                                <div class="position-absolute top-50 translate-middle-y search-icon px-3"><i class="bi bi-lock-fill"></i></div>
                                                <input type="password" name="password" required class="form-control radius-30 ps-5" id="inputChoosePassword" placeholder=" @lang('text.Password')">
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="d-grid">
                                                <button type="submit" class="btn btn-main borderRadius-90"> @lang('text.login')</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!--end page main-->

</div>
<!--end wrapper-->


<!--plugins-->
<script src="{{asset('admin_dashboard/assets/js/jquery.min.js')}}"></script>
<script src="{{asset('admin_dashboard/assets/js/pace.min.js')}}"></script>


</body>

</html>
