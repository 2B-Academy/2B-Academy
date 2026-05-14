<div class="row gy-4">

    <a class="col-lg-4 col-md-6 col-sm-6" href="{{route('front.auth.my-courses')}}">
        <div class="card-item bg-white px-20 py-20 rounded-10">
            <div class="d-flex align-items-center gap-16 justify-content-between flex-wrap mb-16">
                <div>
                    <span class="text-14 fw-normal text-neutral-400 mb-4">دوراتي</span>
                    <h6 class="text-18 fw-semibold mb-0">{{$stats['courses']}}</h6>
                </div>
                <div class="w-44 h-44 bg-main-600 rounded-circle text-white text-20 d-flex align-items-center justify-content-center">
                    <img src="{{asset('front/assets/images/icons/dashbord-item1.png')}}" alt="">
                </div>
            </div>
        </div>
    </a>

    <a class="col-lg-4 col-md-6 col-sm-6" href="{{route('front.auth.my-exams')}}">
        <div class="card-item bg-white px-20 py-20 rounded-10">
            <div class="d-flex align-items-center gap-16 justify-content-between flex-wrap mb-16">
                <div>
                    <span class="text-14 fw-normal text-neutral-400 mb-4">اختباراتي</span>
                    <h6 class="text-18 fw-semibold mb-0">{{$stats['exams']}}</h6>
                </div>
                <div class="w-44 h-44 bg-success-600 rounded-circle text-white text-20 d-flex align-items-center justify-content-center">
                    <img src="{{asset('front/assets/images/icons/dashbord-item2.png')}}" alt="">
                </div>
            </div>
        </div>
    </a>

    <a class="col-lg-4 col-md-6 col-sm-6" href="{{route('front.auth.my-lectures-questions')}}">
        <div class="card-item bg-white px-20 py-20 rounded-10">
            <div class="d-flex align-items-center gap-16 justify-content-between flex-wrap mb-16">
                <div>
                    <span class="text-14 fw-normal text-neutral-400 mb-4">أسئلة المحاضرات</span>
                    <h6 class="text-18 fw-semibold mb-0">{{$stats['questions']}}</h6>
                </div>
                <div class="w-44 h-44 bg-warning-600 rounded-circle text-white text-20 d-flex align-items-center justify-content-center">
                    <img src="{{asset('front/assets/images/icons/dashbord-item2.png')}}" alt="">
                </div>
            </div>
        </div>
    </a>

    <a class="col-lg-4 col-md-6 col-sm-6" href="{{route('front.auth.my-certificates')}}">
        <div class="card-item bg-white px-20 py-20 rounded-10">
            <div class="d-flex align-items-center gap-10 justify-content-between flex-wrap mb-16">
                <div>
                    <span class="text-14 fw-normal text-neutral-400 mb-4">شهاداتي</span>
                    <h6 class="text-18 fw-semibold mb-0">{{$stats['certificates']}}</h6>
                </div>
                <div class="w-44 h-44 bg-neutral-900 rounded-circle text-white text-20 d-flex align-items-center justify-content-center">
                    <img src="{{asset('front/assets/images/icons/dashbord-item4.png')}}" alt="">
                </div>
            </div>
        </div>
    </a>


    <a class="col-lg-4 col-md-6 col-sm-6" href="{{route('front.auth.my-ratings')}}">
        <div class="card-item bg-white px-20 py-20 rounded-10">
            <div class="d-flex align-items-center gap-16 justify-content-between flex-wrap mb-16">
                <div>
                    <span class="text-14 fw-normal text-neutral-400 mb-4">تعليقاتي</span>
                    <h6 class="text-18 fw-semibold mb-0">{{$stats['ratings']}}</h6>
                </div>
                <div class="w-44 h-44 bg-warning-600 rounded-circle text-white text-20 d-flex align-items-center justify-content-center">
                    <img src="{{asset('front/assets/images/icons/dashbord-item2.png')}}" alt="">
                </div>
            </div>
        </div>
    </a>


    <div class="col-lg-4 col-md-6 col-sm-6">
        <div class="card-item bg-white px-20 py-20 rounded-10">
            <div class="d-flex align-items-center gap-16 justify-content-between flex-wrap mb-16">
                <div>
                    <span class="text-14 fw-normal text-neutral-400 mb-4">عدد الساعات المكتمله في {{date('Y')}}</span>
                    @php
                        $totalHours = $settings['yearly_hours'] ?? 60;
                        $achievedHours = round($stats['year_hours']);
                        $percentage = ($achievedHours / $totalHours) * 100;
                    @endphp
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar bg-warning"
                             role="progressbar"
                             style="width: {{ $percentage }}%;"
                             aria-valuenow="{{ $percentage }}"
                             aria-valuemin="0"
                             aria-valuemax="100">
                            {{  min(round($percentage), 100) }}%
                        </div>
                    </div>
                    <small class="text-muted mt-2 d-block text-center">
                        {{ $achievedHours }} من {{ $totalHours }} ساعة
                    </small>
                </div>
                <div class="w-44 h-44 bg-dark rounded-circle text-white text-20 d-flex align-items-center justify-content-center">
                    <img src="{{asset('front/assets/images/icons/check.png')}}" alt="">
                </div>
            </div>
        </div>
    </div>

</div>
