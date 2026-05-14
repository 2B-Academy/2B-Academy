<div class="col-md-12">
    <div class="table-responsive text-center mt-4">
        <table class="table align-middle table-hover attendance-user-table">
            <thead class="table-secondary">
            <tr>
                <th colspan="11">
                    <div class="d-flex align-items-center justify-content-between">
                        <strong dir="ltr">{{$title}}</strong>
                        <a class="btn btn-success btn-sm" href="{{route('admin.attendances.export', $type)}}?from={{request('from')}}&to={{request('to')}}">
                            <i class="lni lni-files mx-1"></i> تصدير
                        </a>
                    </div>
                </th>
            </tr>
            <tr>
                <Th>كود الموظف</Th>
                <Th>الأسم</Th>
                <Th>القسم</Th>
                <Th>الدورة التدريبية</Th>
                <Th>المجموعة</Th>
                <Th>الفئه</Th>
                <Th>عدد السيشنز المطلوبة</Th>
                <Th>حضر كام سيشن</Th>
                <Th>عدد ساعات السيشن الواحدة </Th>
                <th>عدد ساعات الحضور</th>
{{--                <th>إجمالي ساعات الدورة التدريبية</th>--}}
            </tr>
            </thead>
            <tbody>
            @forelse($content as $con)
                <tr>
                    <td>{{$con->employee_code}}</td>
                    <td>{{$con->employee_name}}</td>
                    <td>{{$con->user_department}}</td>
                    <td>{{$con->course_name}}</td>
                    <td>{{$con->group_name ?? ''}}</td>
                    <td>{{$con->course_category_name}}</td>
                    <td>{{$con->sessions_count > 0 ? $con->sessions_count : 1}}</td>
                    <td>{{$con->user_attendance_count}}</td>
                    <td>{{ round($con->attendance_hours / $con->user_attendance_count) }}</td>
                    <td>{{round($con->attendance_hours)}}</td>
{{--                    <td>{{$con->total_hours}}</td>--}}
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="text-center">
                        <p>لا يوجد بيانات</p>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
