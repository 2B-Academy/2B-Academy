<div class="col-md-12">
    <div class="table-responsive text-center mt-4">
        <table class="table align-middle table-hover attendance-employee-table">
            <thead class="table-secondary">
            <tr>
                <th colspan="3">
                    <div class="d-flex align-items-center justify-content-between">
                        <strong dir="ltr">{{$title}}</strong>
                        <a class="btn btn-success btn-sm" href="{{route('admin.attendances.export', $type)}}?from={{request('from')}}&to={{request('to')}}">
                            <i class="lni lni-files mx-1"></i> تصدير
                        </a>
                    </div>
                </th>
            </tr>
            <tr>
                <Th>الكود</Th>
                <th>الأسم</th>
                <th> عدد ساعات الحضور</th>
            </tr>
            </thead>
            <tbody>
            @forelse($content as $con)
                <tr>
                    <td>{{$con->user_machine_code}}</td>
                    <td>{{$con->name}}</td>
                    <td class="total">{{round($con->total_attendance_hours)}}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">
                        <p>لا يوجد بيانات</p>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
