<!--start top header-->
<header class="top-header">
    <nav class="navbar navbar-expand gap-3">
        <div class="mobile-toggle-icon fs-3">
            <i class="bi bi-list"></i>
        </div>

        <div class="top-navbar-right ms-auto">
            <ul class="navbar-nav align-items-center">
            </ul>
        </div>
        <div class="dropdown dropdown-user-setting">
            <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                <div class="user-setting d-flex align-items-center gap-3">
                    <i class="lni lni-user avatarHeader"></i>
                    <div class="d-none d-sm-block">
                        <p class="user-name mb-0">{{auth()->user()->name}}</p>
                        <small class="mb-0 dropdown-user-designation"></small>
                    </div>
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="{{route('admin.userProfile')}}">
                        <div class="d-flex align-items-center">
                            <div class=""><i class="bi bi-person-fill"></i></div>
                            <div class="ms-3"><span>بياناتي </span></div>
                        </div>
                    </a>
                </li>

                <li><hr class="dropdown-divider"></li>

                <li>
                    <a class="dropdown-item" href="{{ route('admin.logout') }}"
                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">

                        <div class="d-flex align-items-center">
                            <div class=""><i class="bi bi-lock-fill"></i></div>
                            <div class="ms-3"><span>تسجيل الخروج</span></div>
                        </div>
                    </a>
                    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>
        </div>
    </nav>
</header>
<!--end top header-->
