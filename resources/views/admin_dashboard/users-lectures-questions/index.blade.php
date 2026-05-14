@extends('admin_dashboard.layout.master')
@section('Page_Title')  أسئلة المحاضرات @endsection

@section('content')


    <div class="card">
        <div class="card-body">

            <div class="d-flex align-items-center">
                <h5 class="mb-0"> <i class="bi bi-grid-fill"></i>  أسئلة المحاضرات </h5>
            </div>

            <div class="mt-5 mb-2 row">
                @include('admin_dashboard.includes.filterByUsersAndCourses',['courses' => true, 'users' => true, 'answered_filter' => true])
            </div>

            <div class="table-responsive mt-4">
                <table class="table align-middle table-hover">
                    <thead class="table-secondary">
                    <tr>
                        <th>#</th>
                        <Th>الدورة التدريبية</Th>
                        <Th>الموظف</Th>
                        <th>السؤال</th>
                        <th width="30%">الإجابة</th>
                        <th>من قام بالاجابة</th>
                        <th>التحكم</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($content as $con)
                        <tr>
                            <td>{{$con->id}}</td>
                            <td>{{$con->course?->title}} - {{$con->lecture?->title}}</td>
                            <td>{{$con->user?->name}}</td>
                            <td>{{$con->question}}</td>
                            <td>
                                <form method="post" id="addAnswer" action="{{route('admin.user.lecture-question.update', $con->id)}}">
                                    @csrf
                                    <textarea rows="2" cols="2" class="form-control" name="answer" placeholder="الرد على السؤال" required>{{$con->answer}}</textarea>
                                    <button type="submit" class="mt-2 btn btn-success btn-sm">حفظ</button>
                                </form>
                            </td>
                            <td>{{$con->answeredBy?->name}}</td>
                            <td>
                                <a href="{{ route('admin.user.lecture-question.destroy', $con->id) }}" class="delete course_button"><i
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
