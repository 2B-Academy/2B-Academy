@extends('admin_dashboard.layout.master')
@section('Page_Title')  تقارير التقييم العام @endsection

@push('css')
    <style>
        .table-secondary th {
            font-size: 11px;
        }
        .title-header
        {
            background: #313c4c;
            padding: 15px;
            margin-bottom: 0;
            color: #d9d9d9;
            font-size: 15px;
        }
    </style>
@endpush
@section('content')


    <div class="card">
        <div class="card-body">

            <div class="d-flex align-items-center">
                <h5 class="mb-0"> <i class="bi bi-grid-fill"></i>  تقارير التقييم العام </h5>
            </div>

            <form class="row mt-5" method="GET" action="">
                <div class="col-md-3">
                    <select class="form-control form-select" name="year">
                        <option value="">السنة</option>
                        @for($i=2025; $i <= 2030; $i++)
                            <option @selected($i == request('year')) value="{{$i}}">{{$i}}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-control form-select" name="month">
                        <option value="">الشهر</option>
                        @for($i=1; $i <= 12; $i++)
                            <option @selected($i == request('month')) value="{{$i}}">{{$i}}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-control form-select" name="course_id">
                        <option value="">اختر الدورة التدريبية</option>
                        @foreach($allCourses as $key => $val)
                            <option @selected($key == request('course_id')) value="{{$key}}">{{$val}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-main w-100">فلتر</button>
                </div>

            </form>

            <div class="table-responsive mt-4">
                <h3 class="title-header d-flex align-items-center justify-content-between">
                    <span>معدل التقييم لكل سؤال / محاضر</span>
                    <button type="button" class="btn btn-success btn-sm" onclick="exportData('{{route('admin.evaluations-reports.export.per_question')}}')">
                        <i class="lni lni-files mx-1"></i> تصدير
                    </button>
                </h3>
                <table class="table align-middle table-hover">
                    <thead class="table-secondary">
                    <tr>
                        <th>المحاضر</th>
                        @foreach($questions as $question)
                            <th>{{ $question }}</th>
                        @endforeach
                        <th>Overall</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($pivot as $instructor => $data)
                        <tr>
                            <td>{{ $instructor }}</td>
                            @foreach($questions as $question)
                                <td>{{ $question ? number_format($data['questions'][$question], 2) : '-' }}</td>
                            @endforeach
                            <td><strong>{{ number_format($data['overall'], 2) }}</strong></td>
                        </tr>
                    @endforeach
                    <tr class="table-primary fw-bold">
                        <td>Grand Total</td>
                        @foreach($questions as $question)
                            <td>{{ $question ? number_format($grandTotal['questions'][$question], 2) : 0 }}</td>
                        @endforeach
                        <td>{{ $grandTotal['overall'] }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <hr>
            <div class="row">
                <div class="col-12">
                    <div class="charts-courses">
                        <canvas id="topInstructors" class="w-100"></canvas>
                    </div>
                </div>
            </div>
            <hr>

            <div class="table-responsive mt-4">
                <h3 class="title-header d-flex align-items-center justify-content-between">
                    <span>معدل التقييم لكل قسم / محاضر</span>
                    <button type="button" class="btn btn-success btn-sm" onclick="exportData('{{route('admin.evaluations-reports.export.per_category')}}')">
                        <i class="lni lni-files mx-1"></i> تصدير
                    </button>
                </h3>
                <table class="table align-middle table-hover">
                    <thead class="table-secondary">
                    <tr>
                        <th>فئة التقييم</th>
                        <th>اسم المحاضر</th>
                        <th>متوسط التقييم</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($avgPerInstructorCategoryGrouped as $evaluation)
                        @foreach($evaluation['instructors'] as $index => $instructor)
                            <tr>
                                @if($index == 0)
                                    <td rowspan="{{ count($evaluation['instructors']) }}" class="align-middle bg-light">
                                        <strong>{{ $evaluation['evaluation_category_name'] }}</strong>
                                    </td>
                                @endif
                                <td>{{ $instructor['instructor_name'] }}</td>
                                <td>
                                    <span class="badge bg-{{ $instructor['avg_rate'] >= 4 ? 'success' : ($instructor['avg_rate'] >= 3 ? 'warning' : 'danger') }}">
                                        {{ number_format($instructor['avg_rate'], 2) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                    </tbody>
                </table>
            </div>


            <hr>


            <div class="table-responsive mt-4">
                <h3 class="title-header d-flex align-items-center justify-content-between">
                    <span>الأسئلة والإجابات الكتابية</span>
                    <button type="button" class="btn btn-success btn-sm" onclick="exportData('{{route('admin.evaluations-reports.export.per_text')}}')">
                        <i class="lni lni-files mx-1"></i> تصدير
                    </button>
                </h3>
                <table class="table align-middle table-hover">
                    <thead class="table-secondary">
                    <tr>
                        <th>السؤال</th>
                        <th>الإجابة</th>
                        <th>القسم</th>
                        <th>المحاضر</th>
                        <th>الدورة التدريبية</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($text_questions as $question)
                            <tr>
                                <td>{{ $question->evaluation_title }}</td>
                                <td>{{ $question->answer }}</td>
                                <td>{{ $question->evaluation_category_name }}</td>
                                <td>{{ $question->instructor_name }}</td>
                                <td>{{ $question->course_name }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>


@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx1 = document.getElementById('topInstructors');

        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: [
                    @foreach($topInstructors as $instructor)
                        '{{$instructor->instructor_name}} '+' ('+ {{number_format($instructor->avg_rate, 2)}}+' )',
                    @endforeach
                ],
                datasets: [{
                    label: "  المحاضرين الأعلي تقييماً   ",
                    data:
                        [@foreach($topInstructors as $instructor) {{$instructor->avg_rate}}, @endforeach],
                    borderWidth: 1,
                    borderColor: ['#ffffff','#ffffff', '#ffffff', '#ffffff', '#ffffff', '#ffffff'], // Add custom color border
                    backgroundColor: ['#081b67'], // Add custom color border
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

    <script>
        function exportData(url_path) {
            // جلب القيم من الفورم
            const courseId = document.querySelector('select[name="course_id"]').value;
            const month = document.querySelector('select[name="month"]').value;
            const year = document.querySelector('select[name="year"]').value;

            // بناء الـ URL مع الفلاتر
            let url = url_path + '?';

            if (courseId) url += 'course_id=' + courseId + '&';
            if (month) url += 'month=' + month + '&';
            if (year) url += 'year=' + year + '&';

            // إزالة آخر &
            url = url.slice(0, -1);

            // فتح الرابط
            window.location.href = url;
        }
    </script>
@endpush
