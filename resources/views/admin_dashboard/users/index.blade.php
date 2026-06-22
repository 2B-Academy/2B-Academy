@extends('admin_dashboard.layout.master')
@section('Page_Title')  الموظفين @endsection

@section('content')


    <div class="card">
        <div class="card-body">

            <div class="d-flex align-items-center justify-content-between">
                <h5 class="mb-0"> <i class="bi bi-grid-fill"></i>  الموظفين </h5>
{{--                <a href="{{route('admin.users.sync')}}" class="btn btn-success">تحديث الموظفين من سيستم الموارد البشرية</a>--}}
                <div class="ms-auto position-relative">
                    <a href="{{route('admin.users.create')}}" class="btnIcon btn btn-outline-primary px-5"><i class="lni lni-circle-plus"></i> إنشاء </a>
                </div>
            </div>

            <form action="{{route('admin.users.index')}}" method="GET" class="gap-3 d-lg-flex align-items-center justify-content-center mt-3 mb-2">
                <input type="text" name="search" class="form-control" value="{{request('search')}}" placeholder="ابحث بالأسم أو بالبريد الإلكتروني او الكود او القسم....">
                <button type="submit" class="btn btn-primary w-50">
                    <i class="lni lni-search mx-1"></i> بحث
                </button>
            </form>

            <div class="table-responsive mt-4">
                <table class="table align-middle table-hover">
                    <thead class="table-secondary">
                    <tr>
                        <th>كود الموظف</th>
                        <Th>الأسم</Th>
                        <th>البريد الإلكتروني</th>
                        <th>القسم</th>
                        <th>المسمى الوظيفي</th>
                        <th>التحكم</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($content as $con)
                        <tr>
                            <td>{{$con->machine_code}}</td>
                            <td>{{$con->name}}</td>
                            <td>{{$con->email}}</td>
                            <td>{{$con->department_name}}</td>
                            <td>{{$con->job_title}}</td>
                            <td>
                                <div class="table-actions d-flex align-items-center gap-3 fs-6">
                                    <a href="{{route('admin.users.show', $con->id)}}" class="text-primary" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                       title="مشاهدة"><i class="lni lni-eye"></i></a>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                <p>لا يوجد بيانات</p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                <div>
                    {{ $content->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>


@endsection

@push('js')
    @include('admin_dashboard.components.delete')
@endpush
