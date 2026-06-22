<!-- ==================== Mobile Menu Start Here ==================== -->
<div class="mobile-menu scroll-sm d-lg-none d-block">
    <button type="button" class="close-button"><i class="ph ph-x"></i> </button>
    <div class="mobile-menu__inner">
        <a href="{{route('front.home')}}" class="mobile-menu__logo">
            <img src="{{ getFullPath($settings['header_logo'] ?? '') ?: asset('front/assets/images/logo/orange_dark.png') }}"  width="50px">
        </a>
        <div class="mobile-menu__menu">

            <ul class="nav-menu flex-align nav-menu--mobile">
                <li class="nav-menu__item  {{request()->segment(1) == '' ? 'activePage' : ''}}">
                    <a href="{{route('front.home')}}" class="nav-menu__link">الرئيسية</a>
                </li>
                <li class="nav-menu__item  {{request()->segment(1) == 'about-us' ? 'activePage' : ''}}">
                    <a href="{{route('front.about')}}" class="nav-menu__link">من نحن</a>
                </li>
                <li class="nav-menu__item  {{request()->segment(1) == 'courses' || request()->segment(1) == 'course' ? 'activePage' : ''}}">
                    <a href="{{route('front.courses')}}" class="nav-menu__link">الدورات التدريبية</a>
                </li>
                <li class="nav-menu__item  {{request()->segment(1) == 'instructors' ? 'activePage' : ''}}">
                    <a href="{{route('front.instructors')}}" class="nav-menu__link">المحاضرين</a>
                </li>
                <li class="nav-menu__item  {{request()->segment(1) == 'articles' || request()->segment(1) == 'article' ? 'activePage' : ''}}">
                    <a href="{{route('front.articles')}}" class="nav-menu__link">المقالات</a>
                </li>
                <li class="nav-menu__item  {{request()->segment(1) == 'contact-us' ? 'activePage' : ''}}">
                    <a href="{{route('front.contact')}}" class="nav-menu__link">تواصل معنا</a>
                </li>
            </ul>

            <div class="d-sm-none d-block mt-24">
                <div class="header-select border border-neutral-30 bg-main-25 rounded-pill position-relative">
                    <span class="select-icon d-xxl-block d-none position-absolute top-50 translate-middle-y inset-inline-start-0 z-1 ms-lg-4 ms-12 text-xl pointer-event-none d-flex">
                        <i class="ph-bold ph-squares-four"></i>
                    </span>
                    <select class="js-example-basic-single border-0 select_category" name="category">
                        <option value="1" selected disabled>الأقسام</option>
                        @foreach($front_categories as $category)
                        <option value="{{$category->id}}">{{$category->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- ==================== Mobile Menu End Here ==================== -->


<!-- ==================== Header Start Here ==================== -->
<header class="header">
    <div class="container container">
        <nav class="header-inner flex-between gap-8">

            <div class="header-content-wrapper flex-align flex-grow-1">
                <!-- Logo Start -->
                <div class="logo">
                    <a href="{{route('front.home')}}" class="link">
                        <img src="{{ getFullPath($settings['header_logo'] ?? '') ?: asset('front/assets/images/logo/orange_dark.png') }}">
                    </a>
                </div>
                <!-- Logo End  -->

                <!-- Select Start -->
                <div class="d-sm-block d-none">
                    <div class="header-select border border-neutral-30 bg-main-25 rounded-pill position-relative">
                            <span class="select-icon d-xxl-block d-none position-absolute top-50 translate-middle-y inset-inline-start-0 z-1 ms-lg-4 ms-12 text-xl pointer-event-none d-flex">
                                <i class="ph-bold ph-squares-four"></i>
                            </span>
                        <select class="js-example-basic-single border-0 select_category" name="category">
                            <option value="1" selected disabled>الأقسام</option>
                            @foreach($front_categories as $category)
                                <option value="{{$category->id}}">{{$category->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <!-- Select End -->

                <!-- Menu Start  -->
                <div class="header-menu d-lg-block d-none">

                    <ul class="nav-menu flex-align ">
                        <li class="nav-menu__item  {{request()->segment(1) == '' ? 'activePage' : ''}}">
                            <a href="{{route('front.home')}}" class="nav-menu__link">الرئيسية</a>
                        </li>
                        <li class="nav-menu__item  {{request()->segment(1) == 'about-us' ? 'activePage' : ''}}">
                            <a href="{{route('front.about')}}" class="nav-menu__link">من نحن</a>
                        </li>
                        <li class="nav-menu__item  {{request()->segment(1) == 'courses' || request()->segment(1) == 'course' ? 'activePage' : ''}}">
                            <a href="{{route('front.courses')}}" class="nav-menu__link">الدورات التدريبية</a>
                        </li>
                        <li class="nav-menu__item  {{request()->segment(1) == 'instructors' ? 'activePage' : ''}}">
                            <a href="{{route('front.instructors')}}" class="nav-menu__link">المحاضرين</a>
                        </li>
                        <li class="nav-menu__item  {{request()->segment(1) == 'articles' || request()->segment(1) == 'article' ? 'activePage' : ''}}">
                            <a href="{{route('front.articles')}}" class="nav-menu__link">المقالات</a>
                        </li>
                        <li class="nav-menu__item  {{request()->segment(1) == 'contact-us' ? 'activePage' : ''}}">
                            <a href="{{route('front.contact')}}" class="nav-menu__link">تواصل معنا </a>
                        </li>
                    </ul>
                </div>
                <!-- Menu End  -->
            </div>

            <!-- Header Right start -->
            <div class="header-right flex-align">

                @guest
                <a href="{{route('front.auth.login')}}" class="btn-sm btn btn-main">
                    <i class="ph ph-user-circle text-white"></i>
                    تسجيل الدخول
                </a>
                @else
                    <a href="{{ route('front.auth.dashboard') }}" class="btn-sm btn btn-main px-5 py-10">
                        ملفي الشخصي
                    </a>
                    <a href="{{ route('front.auth.logout') }}" class="btn-sm btn btn-dark px-5 py-10" onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();">
                       تسجيل الخروج
                    </a>
                    <form id="logout-form" action="{{ route('front.auth.logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                @endguest
                <button type="button" class="toggle-mobileMenu d-lg-none text-neutral-200 flex-center">
                    <i class="ph ph-list"></i>
                </button>
            </div>
            <!-- Header Right End  -->
        </nav>
    </div>
</header>
<!-- ==================== Header End Here ==================== -->
