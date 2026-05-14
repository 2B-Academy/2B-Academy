@extends('admin_dashboard.layout.master')
@section('Page_Title')  مستخدمين الدورات الأوفلاين @endsection

@section('content')


    <div class="card">
        <div class="card-body">

            <div class="d-flex align-items-center">
                <h5 class="mb-0"> <i class="bi bi-grid-fill"></i>  مستخدمين الدورات الأوفلاين </h5>
                <div class="ms-auto position-relative">
                    <a href="{{route('admin.users-courses-offline.create')}}" class="btnIcon btn btn-outline-primary px-5"><i class="lni lni-circle-plus"></i> إنشاء </a>
                </div>
            </div>

            <div class="mt-5 mb-2 row">
                <form method="GET" action="" class="col-12">
                    <div class="row m-0 align-items-end">
                        <div class="col-md-4 mb-2">
                            <label for="course">الدورة التدريبية:</label>
                            <select name="course_id" id="course_id" class="form-control select2">
                                <option value=""> الدورات التدريبية</option>
                                @foreach($allCourses as $id => $title)
                                    <option value="{{ $id }}" {{ $selectedCourse == $id ? 'selected' : '' }}>
                                        {{ $title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label for="group_id">المجموعة:</label>
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
                        <div class="col-md-4 mb-2">
                            <button type="submit" class="btn btn-main">فلتر</button>
                            <button type="reset" onclick="location.href='{{route('admin.users-courses-offline.index')}}'" class="mx-2 btn btn-danger">إلغاء</button>
                        </div>


                    </div>
                </form>

            </div>

            <div class="table-responsive mt-4">
                <table class="table align-middle table-hover">
                    <thead class="table-secondary">
                    <tr>
                        <th>#</th>
                        <th>الموظف</th>
                        <th>القسم</th>
                        <Th>الدورة التدريبية</Th>
                        <Th>المجموعة</Th>
                        <Th>التحكم</Th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($content as $con)
                        <tr>
                            <td>{{$con->user?->machine_code}}</td>
                            <td>{{$con->user?->name}}</td>
                            <td>{{$con->user?->department_name}}</td>
                            <td>{{$con->course?->title}}</td>
                            <td>{{$con->group?->name}}</td>
                            <td>
                                <a href="{{route('admin.users-courses-offline.destroy', $con->id)}}" class="delete text-danger" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                   title="حذف"><i class="bi bi-trash-fill"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">
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
    @include('admin_dashboard.components.delete')
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
