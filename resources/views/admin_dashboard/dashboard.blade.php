@extends('admin_dashboard.layout.master')

@section('Page_Title')  Dashboard  @endsection

@section('content')

    <div class="row mb-3">
        <div class="col-12">
            @include('errors.validation_error')
            @if(in_array(auth()->user()->email , ['dev.mohamedsaid@gmail.com', 'zidan@lms.com']))
                <form action="{{ route('admin.sync-employees-job') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success" style="float: left">
                        <i class="bx bx-plus mx-1"></i> جلب الموظفين الجدد
                    </button>
                </form>
            @endcan
        </div>

    </div>

    <div class="row mb-2 justify-content-between align-items-center">
        <a class="col-md-3" href="{{route('admin.courses.index')}}">
            <div class="card radius-10 bg-orange">
                <div class="card-body text-center">
                    <div class="widget-icon mx-auto mb-3 bg-white-1 text-white">
                        <i class="bx bx-building"></i>
                    </div>
                    <h3 class="text-white">{{$statistics->courses}}</h3>
                    <p class="mb-0 text-white">الدورات التدريبية</p>
                </div>
            </div>
        </a>
        <a class="col-md-3" href="{{route('admin.users.index')}}">
            <div class="card radius-10 bg-primary">
                <div class="card-body text-center">
                    <div class="widget-icon mx-auto mb-3 bg-white-1 text-white">
                        <i class="bx bx-building-house"></i>
                    </div>
                    <h3 class="text-white">{{$statistics->users}}</h3>
                    <p class="mb-0 text-white">الموظفين</p>
                </div>
            </div>
        </a>
        <a class="col-md-3" href="{{route('admin.instructors.index')}}">
            <div class="card radius-10 bg-purple">
                <div class="card-body text-center">
                    <div class="widget-icon mx-auto mb-3 bg-white-1 text-white">
                        <i class="bx bx-building"></i>
                    </div>
                    <h3 class="text-white">{{$statistics->instructors}}</h3>
                    <p class="mb-0 text-white">المحاضرين</p>
                </div>
            </div>
        </a>
        <a class="col-md-3" href="{{route('admin.blogs.index')}}">
            <div class="card radius-10 bg-dark">
                <div class="card-body text-center">
                    <div class="widget-icon mx-auto mb-3 bg-white-1 text-white">
                        <i class="bx bx-building-house"></i>
                    </div>
                    <h3 class="text-white">{{$statistics->articles}}</h3>
                    <p class="mb-0 text-white">المقالات</p>
                </div>
            </div>
        </a>


        <a class="col-md-3" href="{{route('admin.users-courses-ratings.index')}}">
            <div class="card radius-10 bg-warning">
                <div class="card-body text-center">
                    <div class="widget-icon mx-auto mb-3 bg-white-1 text-white">
                        <i class="bx bx-star"></i>
                    </div>
                    <h3 class="text-white">{{$statistics->ratings}}</h3>
                    <p class="mb-0 text-white">التقييمات</p>
                </div>
            </div>
        </a>
        <a class="col-md-3" href="{{route('admin.users-lectures-questions.index')}}">
            <div class="card radius-10 bg-secondary">
                <div class="card-body text-center">
                    <div class="widget-icon mx-auto mb-3 bg-white-1 text-white">
                        <i class="bx bx-question-mark"></i>
                    </div>
                    <h3 class="text-white">{{$statistics->lecturesQuestions}}</h3>
                    <p class="mb-0 text-white">كل أسئلة المحاضرات</p>
                </div>
            </div>
        </a>
        <a class="col-md-3" href="{{route('admin.users-lectures-questions.index')}}?answered=no">
            <div class="card radius-10 bg-danger">
                <div class="card-body text-center">
                    <div class="widget-icon mx-auto mb-3 bg-white-1 text-white">
                        <i class="bx bx-question-mark"></i>
                    </div>
                    <h3 class="text-white">{{$statistics->lecturesQuestionsNotAnswered}}</h3>
                    <p class="mb-0 text-white">أسئلة المحاضرات الغير مجابة</p>
                </div>
            </div>
        </a>
        <a class="col-md-3" href="{{route('admin.users-courses-assignments.index')}}">
            <div class="card radius-10 bg-success">
                <div class="card-body text-center">
                    <div class="widget-icon mx-auto mb-3 bg-white-1 text-white">
                        <i class="bx bx-question-mark"></i>
                    </div>
                    <h3 class="text-white">{{$statistics->usersAssignments}}</h3>
                    <p class="mb-0 text-white">مهام الموظفين</p>
                </div>
            </div>
        </a>

    </div>

    <div class="row my-5 justify-content-between align-items-center">
        <div class="charts-courses">
            <canvas id="coursesUsersChart" class="w-100"></canvas>
        </div>
    </div>



@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx1 = document.getElementById('coursesUsersChart');

        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: [
                    @foreach($chart_courses_with_users as $course)
                        '{{$course->title}} '+' ('+ {{$course->users_count}}+' طلاب)',
                    @endforeach
                ],
                datasets: [{
                    label: "  أكثر 10 دورات مسجل عليهم طلاب   ",
                    data:
                        [@foreach($chart_courses_with_users as $course) {{$course->users_count}}, @endforeach],
                    borderWidth: 1,
                    borderColor: ['#ffffff','#ffffff', '#ffffff', '#ffffff', '#ffffff', '#ffffff'], // Add custom color border
                    backgroundColor: ['#4c5886'], // Add custom color border
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
            }
        });
    </script>
@endpush
