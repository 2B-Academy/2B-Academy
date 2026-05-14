@extends('admin_dashboard.layout.master')
@section('Page_Title')  التقييمات @endsection

@section('content')


    <div class="card">
        <div class="card-body">

            <div class="d-flex align-items-center">
                <h5 class="mb-0"> <i class="bi bi-grid-fill"></i>  التقييمات </h5>
            </div>

            <div class="mt-5 mb-2 row">
                @include('admin_dashboard.includes.filterByUsersAndCourses',['courses' => true, 'users' => true, 'answered_filter' => false])
            </div>

            <div class="table-responsive mt-4">
                <table class="table align-middle table-hover">
                    <thead class="table-secondary">
                    <tr>
                        <th>#</th>
                        <th>الدورة التدريبية</th>
                        <th>الموظف</th>
                        <th>التقييم</th>
                        <th>التعليق</th>
                        <th>التحكم</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($content as $con)
                        <tr>
                            <td>{{$con->id}}</td>
                            <td>{{$con->course?->title}}</td>
                            <td>{{$con->user?->name}}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    @for($i = 1; $i <= (int)$con->rating ; $i++)
                                        <span class="text-15 fw-medium text-main d-flex" id="{{$i}}"><i class="lni lni-star-filled text-warning"></i></span>
                                    @endfor
                                </div>
                            </td>
                            <td>{{$con->comment}}</td>
                            <td>
                                <a href="{{ route('admin.user.ratings.destroy', $con->id) }}" class="delete course_button"><i
                                        class="bi bi-trash-fill mx-1"></i> حذف</a>
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
@endpush
