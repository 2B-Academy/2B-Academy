@extends('admin_dashboard.layout.master')
@section('Page_Title')  الحضور @endsection
@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datepicker/1.0.10/datepicker.min.css" integrity="sha512-YdYyWQf8AS4WSB0WWdc3FbQ3Ypdm0QCWD2k4hgfqbQbRCJBEgX0iAegkl2S1Evma5ImaVXLBeUkIlP6hQ1eYKQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endpush
@section('content')


    <div class="card">
        <div class="card-body">

            <div class="d-flex align-items-center ">
                <h5 class="mb-0"> <i class="bi bi-grid-fill"></i>  الحضور </h5>
                <div class="ms-auto position-relative">
                    <a href="{{ route('admin.attendances.create') }}" class="btnIcon btn btn-outline-primary px-5"><i
                            class="lni lni-circle-plus"></i> إنشاء </a>
                </div>
            </div>

            <div class="row">

                <!--filter by year-->
                <div class="col-12">
                    <form method="GET" action="" class="years my-4 py-3 d-flex align-items-center justify-content-start gap-3 overflow-auto">
                        <div class="form-group w-100">
                            <label for="from">التاريخ من</label>
                            <input type="text" class="form-control date-picker" name="from" value="{{request('from')}}" required>
                        </div>
                        <div class="form-group w-100">
                            <label for="to">التاريخ إلى</label>
                            <input type="text" class="form-control date-picker" name="to" value="{{request('to')}}" required>
                        </div>
                        <div class="form-group w-100">
                            <button type="submit" class="btn btn-main mt-3 w-50">فلتر</button>
                            <a class="btn btn-dark mt-3" href="{{route('admin.attendances.index')}}">حذف الفلتر</a>
                        </div>

                    </form>
                </div>

                <!--attendance_by_category_name-->
                @include('admin_dashboard.attendances.includes.main_table',[
                    'title'   => 'عدد ساعات الحضور لكل فئه',
                    'content' => $attendance_by_category_name,
                    'type' => 'per_category'
                ])

                <!--attendance_by_department-->
                @include('admin_dashboard.attendances.includes.main_table',[
                    'title'   => 'عدد ساعات الحضور لكل إدارة',
                    'content' => $attendance_by_department,
                    'type' => 'per_department'
                ])

                <!--attendance_by_courses-->
                @include('admin_dashboard.attendances.includes.main_table',[
                    'title'   => 'عدد ساعات الحضور لكل كورس',
                    'content' => $attendance_by_course,
                    'type' => 'per_course'
                ])

                <!--attendance_by_months in year-->
                @include('admin_dashboard.attendances.includes.main_table',[
                    'title'   => date('Y'). ' ' . 'عدد ساعات الحضور في  ',
                    'content' => $attendance_by_month,
                    'type' => 'per_month'
                ])


                <!--attendance_user_courses-->
                @include('admin_dashboard.attendances.includes.master-sheet',[
                    'title'   => 'عدد ساعات الحضور للمستخدمين في كل كورس',
                    'content' => $attendance_user_courses,
                    'type' => 'per_user'
                ])

                <!--attendance_by_employees-->
                @include('admin_dashboard.attendances.includes.table_employees',[
                    'title'   => 'عدد ساعات الحضور لكل موظف',
                    'content' => $attendance_by_users,
                    'type' => 'per_employee'
                ])

            </div>


        </div>
    </div>


@endsection

@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datepicker/1.0.10/datepicker.min.js" integrity="sha512-RCgrAvvoLpP7KVgTkTctrUdv7C6t7Un3p1iaoPr1++3pybCyCsCZZN7QEHMZTcJTmcJ7jzexTO+eFpHk4OCFAg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $('.date-picker').datepicker({
            orientation: "left top",
            todayHighlight: true,
            format:"yyyy-mm-dd"
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.attendance-table').forEach(table => {
                let total = 0;

                table.querySelectorAll('tbody tr td:nth-child(2)').forEach(td => {
                    total += parseFloat(td.textContent) || 0;
                });

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td style="background: #ececec;"><strong>الإجمالي</strong></td>
                    <td style="background: #ececec;"><strong>${Math.round(total)}</strong></td>
                `;

                table.querySelector('tbody').appendChild(tr);
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.attendance-employee-table').forEach(table => {
                let total = 0;

                table.querySelectorAll('tbody tr td:nth-child(3)').forEach(td => {
                    total += parseFloat(td.textContent) || 0;
                });

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td style="background: #ececec;"><strong>الإجمالي</strong></td>
                    <td style="background: #ececec;"><strong></strong></td>
                    <td style="background: #ececec;"><strong>${Math.round(total)}</strong></td>
                `;

                table.querySelector('tbody').appendChild(tr);
            });
        });


    </script>



@endpush
