<!--start sidebar -->
<aside class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header justify-content-between">

        <div>
            <svg width="70" height="70" viewBox="0 0 86 51" fill="none" xmlns="http://www.w3.org/2000/svg">
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
        <div class="toggle-icon "> <i class="bi bi-list"></i>
        </div>

    </div>
    <!--navigation-->
    <ul class="metismenu" id="menu">
        <li>
            <a href="{{ route('admin.dashboard')  }}">
                <div class="parent-icon"><i class="bi bi-house-fill"></i>
                </div>
                <div class="menu-title">@lang('text.dashboard')</div>
            </a>
        </li>


        <li>
            <a href="{{ route('admin.users.index')  }}">
                <div class="parent-icon"><i class="lni lni-users"></i>
                </div>
                <div class="menu-title">الموظفين</div>
            </a>
        </li>

         <li>
            <a href="{{ route('admin.categories.index')  }}">
                <div class="parent-icon"><i class="lni lni-layers"></i>
                </div>
                <div class="menu-title">الأقسام</div>
            </a>
        </li>

        <li>
            <a href="{{ route('admin.instructors.index')  }}">
                <div class="parent-icon"><i class="lni lni-users"></i>
                </div>
                <div class="menu-title">المحاضرين</div>
            </a>
        </li>

        <li>
            <a href="{{ route('admin.qualification-skills.index')  }}">
                <div class="parent-icon"><i class="lni lni-graduation"></i>
                </div>
                <div class="menu-title">{{ __('text.qualification_skills') }}</div>
            </a>
        </li>

        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="lni lni-video"></i>
                </div>
                <div class="menu-title">الدورات التدريبية</div>
            </a>
            <ul class="mx-3">
                <li> <a href="{{ route('admin.courses.index')  }}"><i class="lni lni-video"></i> الدورات التدريبية</a>
                </li>
                <li>
                    <a href="{{ route('admin.users-courses.index')  }}">
                         <i class="lni lni-users"></i>مستخدمين الدورات
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.users-courses-offline.index')  }}">
                        <i class="lni lni-users"></i>مستخدمين الأوفلاين 🔴
                    </a>
                </li>
{{--                <li>--}}
{{--                    <a href="{{ route('admin.users-courses-progress.index')  }}">--}}
{{--                        <i class="lni lni-bar-chart"></i>نسب التقدم للمستخدمين--}}
{{--                    </a>--}}
{{--                </li>--}}
                <li>
                    <a href="{{ route('admin.users-courses-progress.index')  }}">
                        <i class="lni lni-bar-chart"></i>تقرير المستخدمين
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.users-courses-ratings.index')  }}">
                        <i class="lni lni-star-filled"></i>التقييمات
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.users-lectures-questions.index')  }}">
                        <i class="lni lni-question-circle"></i>أسئلة المحاضرات
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.users-courses-assignments.index')  }}">
                        <i class="lni lni-question-circle"></i>مهام الموظفين
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.certificates.index')  }}">
                        <i class="lni lni-certificate"></i>الشهادات
                    </a>
                </li>


            </ul>
        </li>


        <li>
            <a href="{{ route('admin.forms.index')  }}">
                <div class="parent-icon"><i class="bx bx-question-mark"></i>
                </div>
                <div class="menu-title">الأختبارات العامة</div>
            </a>
        </li>


        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="lni lni-star-filled"></i>
                </div>
                <div class="menu-title">التقييم العام</div>
            </a>
            <ul class="mx-3">
                <li>
                    <a href="{{ route('admin.evaluation-categories.index')  }}">
                        <i class="lni lni-list"></i> أقسام التقييم العام
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.evaluations.index')  }}">
                        <i class="lni lni-star"></i>التقييم العام
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.evaluations-reports.index')  }}">
                        <i class="lni lni-files"></i> التقارير
                    </a>
                </li>

            </ul>
        </li>


        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="bx bx-user-check"></i>
                </div>
                <div class="menu-title">الحضور</div>
            </a>
            <ul class="mx-3">
                <li>
                    <a href="{{ route('admin.attendances.qr')  }}">
                        <i class="bx bx-image"></i>  QR Code
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.attendances.index')  }}">
                        <i class="bx bx-user-check"></i> الحضور
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.absences.index')  }}">
                        <i class="bx bx-user-minus"></i> الغياب
                    </a>
                </li>

            </ul>
        </li>





        <li>
            <a href="{{ route('admin.abouts.index')  }}">
                <div class="parent-icon"><i class="bx bx-info-square"></i>
                </div>
                <div class="menu-title">@lang('text.AboutUs')</div>
            </a>
        </li>

        <li>
            <a href="{{ route('admin.blogs.index')  }}">
                <div class="parent-icon"><i class="lni lni-text-align-justify"></i>
                </div>
                <div class="menu-title">@lang('text.Blogs')</div>
            </a>
        </li>

        <li>
            <a href="{{ route('admin.testimonials.index')  }}">
                <div class="parent-icon"><i class="bx bx-message-square-detail"></i>
                </div>
                <div class="menu-title">@lang('text.testimonials')</div>
            </a>
        </li>


        <li>
            <a href="{{ route('admin.contacts.index')  }}">
                <div class="parent-icon"><i class="lni lni-support"></i>
                </div>
                <div class="menu-title">@lang('text.ContactUs')</div>
            </a>
        </li>

        <li>
            <a href="{{ route('admin.settings.index') }}">
                <div class="parent-icon"><i class="lni lni-cog"></i>
                </div>
                <div class="menu-title">@lang('text.settings')</div>
            </a>
        </li>

        <li>
            <a href="{{ route('admin.notifications.index')  }}">
                <div class="parent-icon"><i class="bx bx-notification"></i>
                </div>
                <div class="menu-title">الإشعارات العامة</div>
            </a>
        </li>

        <hr>

        <li>
            <a href="{{ route('admin.videos.index')  }}">
                <div class="parent-icon"><i class="bx bx-video"></i>
                </div>
                <div class="menu-title">@lang('text.videos')</div>
            </a>
        </li>


        <hr>

        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="lni lni-users"></i>
                </div>
                <div class="menu-title">@lang('text.management')</div>
            </a>
            <ul class="mx-3">
                <li> <a href="{{ route('admin.admins.index')  }}"><i class="lni lni-user"></i> @lang('text.admins')</a>
                </li>
                <li> <a href="{{ route('admin.roles.index')  }}"><i class="bx bx-user-plus"></i> @lang('text.roles')</a>
                </li>
            </ul>
        </li>



    </ul>
    <!--end navigation-->
</aside>
<!--end sidebar -->
