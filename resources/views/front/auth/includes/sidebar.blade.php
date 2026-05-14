<!-- ========Dashdord Sidebar start======== -->
<div class="student-overlay-sidebar"></div>
<div class="student-dashboard-sidebar px-20 py-24 max-w-288-px bg-white rounded-10 w-100 position-relative">
    <div class="text-center">
        <h5 class="mb-4 text-neutral-500">مرحباً {{ auth()->user()->name }}</h5>
        <span class="text-neutral-500 text-14 fw-normal">{{auth()->user()->email}} </span>
    </div>
    <span class="w-100 bg-neutral-40 mb-24 mt-24 h-1"></span>
    <div class="overflow-x-auto">
        <div class="student-dashboard min-w-max">
            <ul>
                <li class="mb-8 {{request()->segment(2) == 'dashboard' ? 'active' : ''}}">
                    <a href="{{route('front.auth.dashboard')}}" class="fw-medium d-flex align-items-center text-14 gap-8 text-neutral-500 hover-bg-main-600 px-24 py-10 hover-text-white rounded-12 item-hover flex-wrap">
                        <span class="text-16 text-main-600 item-hover__text transition-03"><i class="ph-bold ph-house"></i></span>
                        صفحتي الشخصية</a>
                </li>

                <li class="mb-8 {{(request()->segment(2) == 'my-courses' || request()->segment(2) == 'course') ? 'active' : ''}}">
                    <a href="{{route('front.auth.my-courses')}}" class="fw-medium d-flex align-items-center text-14 gap-8 text-neutral-500 hover-bg-main-600 px-24 py-10 hover-text-white rounded-12 item-hover flex-wrap">
                        <span class="text-16 text-main-600 item-hover__text transition-03"><i class="ph ph-watch"></i></span>
                        دوراتي التدريببة</a>
                </li>
                <li class="mb-8 {{request()->segment(2) == 'my-ratings' ? 'active' : ''}}">
                    <a href="{{route('front.auth.my-ratings')}}" class="fw-medium d-flex align-items-center text-14 gap-8 text-neutral-500 hover-bg-main-600 px-24 py-10 hover-text-white rounded-12 item-hover flex-wrap">
                        <span class="text-16 text-main-600 item-hover__text transition-03"><i class="ph ph-sparkle"></i></span>
                        تعليقاتي</a>
                </li>
                <li class="mb-8 {{request()->segment(2) == 'my-lectures-questions' ? 'active' : ''}}">
                    <a href="{{route('front.auth.my-lectures-questions')}}" class="fw-medium d-flex align-items-center text-14 gap-8 text-neutral-500 hover-bg-main-600 px-24 py-10 hover-text-white rounded-12 item-hover flex-wrap">
                        <span class="text-16 text-main-600 item-hover__text transition-03"><i class="ph ph-seal-question"></i></span>
                        اسئلة المحاضرات</a>
                </li>
                <li class="mb-8 {{request()->segment(2) == 'my-exams' ? 'active' : ''}}">
                    <a href="{{route('front.auth.my-exams')}}" class="fw-medium d-flex align-items-center text-14 gap-8 text-neutral-500 hover-bg-main-600 px-24 py-10 hover-text-white rounded-12 item-hover flex-wrap">
                        <span class="text-16 text-main-600 item-hover__text transition-03"><i class="ph ph-seal-question"></i></span>
                        اختباراتي</a>
                </li>
                <li class="mb-8 {{request()->segment(2) == 'my-assignments' ? 'active' : ''}}">
                    <a href="{{route('front.auth.my-assignments')}}" class="fw-medium d-flex align-items-center text-14 gap-8 text-neutral-500 hover-bg-main-600 px-24 py-10 hover-text-white rounded-12 item-hover flex-wrap">
                        <span class="text-16 text-main-600 item-hover__text transition-03"><i class="ph ph-seal-question"></i></span>
                        المهام</a>
                </li>
                <li class="mb-8 {{request()->segment(2) == 'my-certificates' ? 'active' : ''}}">
                    <a href="{{route('front.auth.my-certificates')}}" class="fw-medium d-flex align-items-center text-14 gap-8 text-neutral-500 hover-bg-main-600 px-24 py-10 hover-text-white rounded-12 item-hover flex-wrap">
                        <span class="text-16 text-main-600 item-hover__text transition-03"><i class="ph ph-certificate"></i></span>
                        شهاداتي</a>
                </li>

                <li class="mb-8">
                    <a href="{{route('front.auth.logout')}}"
                       onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();" class=" text-14 fw-medium text-neutral-500 d-flex align-items-center gap-8  hover-bg-main-600 px-24 py-10 hover-text-white rounded-12 item-hover flex-wrap bg-white">
                            <span class="text-16 text-main-600 item-hover__text transition-03">
                                <i class="ph ph-sign-out"></i>
                            </span>
                        تسجيل الخروج
                    </a>
                </li>
            </ul>
        </div>
    </div>

</div>
<!-- ========Dashdord Sidebar end======== -->
