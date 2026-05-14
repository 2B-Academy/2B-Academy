@extends('admin_dashboard.layout.master')
@section('Page_Title')  مهام الموظفين @endsection

@section('content')


    <div class="card">
        <div class="card-body">

            <div class="d-flex align-items-center">
                <h5 class="mb-0"> <i class="bi bi-grid-fill"></i>  مهام الموظفين </h5>
            </div>

            <div class="mt-5 mb-2 row">
                @include('admin_dashboard.includes.filterByUsersAndCourses',['courses' => true, 'users' => true, 'answered_filter' => false])
            </div>

            <div class="table-responsive mt-4">
                <table class="table align-middle table-hover">
                    <thead class="table-secondary">
                    <tr>
                        <th>#</th>
                        <Th>الدورة التدريبية</Th>
                        <Th>عنوان المهام</Th>
                        <Th>الموظف</Th>
                        <th>ملف المهام</th>
                        <th>ملف الموظف</th>
                        <th>التقييم</th>
                        <th>التحكم</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($content as $con)
                        <tr>
                            <td>{{$con->id}}</td>
                            <td>{{$con->assignment?->course?->title}} </td>
                            <td>{{$con->assignment?->title}}</td>
                            <td>{{$con->user?->name}}</td>
                            <td>
                                <a href="{{$con->getFileUrl($con->assignment?->file)}}" download class="btn btn-sm btn-secondary btn-sm">
                                    <i class="lni lni-download mx-1"></i> ملف المهام
                                </a>
                            </td>
                            <td>
                                <a href="{{$con->getFileUrl($con->user_file)}}" download class="btn btn-sm btn-primary btn-sm">
                                    <i class="lni lni-download mx-1"></i> ملف الموظف
                                </a>
                            </td>
                            <td class="text-center">{!! $con->feedback ? '<span class="text-success"><i class="lni lni-checkmark"></i></span>' :
                                '<span class="text-danger"><i class="lni lni-close"></i></span>' !!}</td>
                            <td>
                                <a href="{{ route('admin.users-courses-assignments.edit', $con->id) }}" class="btn btn-sm btn-outline-dark mx-2"><i
                                        class="bx bx-edit mx-1"></i>  اضافة تقييم </a>
                                <a href="{{ route('admin.users-courses-assignments.destroy', $con->id) }}" class="delete course_button"><i
                                        class="bi bi-trash-fill mx-1"></i> حذف</a>
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
    @include('admin_dashboard.components.delete')
    <script>
        $(document).on('submit', '#addAnswer', function (e){
            e.preventDefault();
            var url = $(this).attr('action');
            var data = $(this).serialize();
            $.ajax({
                url: url,
                type: 'POST',
                data: data,
                success: function(response) {
                    if(response.status)
                    {
                        swal({
                            title: response.message,
                            text: "شكراً لك.",
                            icon: "success",
                            button: {
                                text: "خروج",
                                value: true,
                                visible: true,
                                closeModal: true
                            }
                        })
                    }
                }
            })
        });
    </script>
@endpush
