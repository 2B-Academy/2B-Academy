@extends('admin_dashboard.layout.master')
@section('Page_Title')  الشهادات @endsection

@section('content')


    <div class="card">
        <div class="card-body">

            <div class="d-flex align-items-center justify-content-between">
                <h5 class="mb-0"> <i class="bi bi-grid-fill"></i>  الشهادات </h5>
                <a href="{{ route('admin.certificates.downloadAll', request()->all()) }}" class="btn btn-success">
                    <i class="lni lni-download mx-1"></i> تحميل الشهادات كملف ZIP
                </a>
            </div>

            <div class="mt-5 mb-2 row">
                <form method="GET" action="" class="col-12">
                    <div class="row m-0">

                        <div class="col-md-6 mb-2">
                            <label for="course">الدورة التدريبية:</label>
                            <select name="course_id" id="course" class="form-control"  onchange="this.form.submit()">
                                <option value="">كل الدورات التدريبية</option>
                                @foreach($allCourses as $id => $title)
                                    <option value="{{ $id }}" {{ $selectedCourse == $id ? 'selected' : '' }}>
                                        {{ $title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>

            </div>

            <div class="table-responsive mt-4">
                <table class="table align-middle table-hover">
                    <thead class="table-secondary">
                    <tr>
                        <th>كود الموظف</th>
                        <Th>الأسم</Th>
                        <Th>القسم</Th>
                        <Th>الدورة التدريبية</Th>
                        <th>التحكم</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($content as $con)
                        <tr>
                            <td>{{$con->user?->machine_code}}</td>
                            <td>{{$con->user?->name}} </td>
                            <td>{{$con->user?->department_name}}</td>
                            <td>{{$con->course?->title}}</td>
                            <td>
                                <a  href="{{ route('admin.certificates.showCertificate', ['course_id' => $con->course_id, 'user_id' => $con->user_id ]) }}" class="course_button">
                                    <i class="lni lni-eye mx-1"></i>  عرض الشهادة</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">
                                <p>لا يوجد بيانات</p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                <div>
                    {{$content->links()}}
                </div>
            </div>
        </div>
    </div>


@endsection

@push('js')
@endpush
