@extends('admin_dashboard.layout.master')
@section('Page_Title')  الغياب @endsection

@section('content')


    <div class="card">
        <div class="card-body">

            <div class="d-flex align-items-center ">
                <h5 class="mb-0"> <i class="bi bi-grid-fill"></i>  الغياب </h5>
            </div>

            <div class="row">

                <!--filter -->
                <div class="col-12">
                    <form method="GET" action="" class="years my-4 py-3 d-flex align-items-center justify-content-start gap-3 overflow-auto">
                        <div class="form-group w-100">
                            <label for="from">حدد الدورة التدريبية</label>
                            <select class="form-control form-select select2" name="course_id" id="course_id" required>
                                <option value="">اختر الدورة التدريبية</option>
                                @foreach($allCourses as $key => $val)
                                    <option @selected($key == request('course_id')) value="{{$key}}">{{$val}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group w-100">
                            <label class="form-label"> اختر المجموعة <small class="text-danger">*</small></label>
                            <select name="group_id" id="group_id" required class="form-control select2">
                                @if(!is_null(request('course_id')) && !is_null(request('group_id')))
                                    @php
                                        $groups = \App\Models\Course::with('sections')->whereId(request('course_id'))->first();
                                    @endphp
                                    @foreach($groups->sections as $group)
                                        <option value="{{$group->id}}" @selected($group->id == request('group_id'))>
                                            {{$group->name}}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="form-group w-100">
                            <button type="submit" class="btn btn-main mt-3 w-50">فلتر</button>
                            <a class="btn btn-dark mt-3" href="{{route('admin.absences.index')}}">إلغاء</a>
                        </div>

                    </form>
                </div>

                @if(request('course_id') && request('group_id'))
                    <div class="col-md-12 mt-4">
                        <div class="table-responsive text-center mt-4">
                            <table class="table align-middle table-hover attendance-user-table">
                                <thead class="table-secondary">
                                <tr>
                                    <th colspan="8">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <strong dir="ltr">الغياب</strong>
                                            <a class="btn btn-success btn-sm" href="{{route('admin.absences.export')}}?course_id={{request('course_id')}}&group_id={{request('group_id')}}">
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
                                    <th>عدد ساعات الحضور</th>
                                    <th>إجمالي ساعات الدورة التدريبية</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($absent_users as $con)
                                    <tr>
                                        <td>{{$con->machine_code}}</td>
                                        <td>{{$con->name}}</td>
                                        <td>{{$con->department_name}}</td>
                                        <td>{{$course->title}}</td>
                                        <td>{{$group->name}}</td>
                                        <td>{{$course->category?->name}}</td>
                                        <td>0</td>
                                        <td>{{$course->hours}}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            <p>لا يوجد بيانات</p>
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

            </div>


        </div>
    </div>


@endsection

@push('js')
    <script>
        $(document).on('change', '#course_id', function (){
            var course_id = $(this).val();
            $.ajax({
                url: "{{route('admin.get-groups-of-course')}}",
                type: 'GET',
                data: {
                    'course_id' : course_id
                },
                success: function(response) {
                    if(response.status)
                    {
                        $('#group_id').html(response.data.html);
                    }
                    else{
                        $('#group_id').html('');
                    }

                }
            })
        });
    </script>
@endpush
