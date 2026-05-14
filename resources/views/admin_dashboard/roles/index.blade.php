@extends('admin_dashboard.layout.master')
@section('Page_Title')  الأدوار @endsection

@section('content')


    <div class="card">
        <div class="card-body">

            <div class="d-flex align-items-center">
                <h5 class="mb-0"> <i class="bi bi-grid-fill"></i> الأدوار </h5>
                <div class="ms-auto position-relative">
                    <a href="{{route('admin.roles.create')}}" class="btnIcon btn btn-outline-primary px-5"><i class="lni lni-circle-plus"></i> إنشاء </a>
                </div>
            </div>

            <div class="table-responsive mt-4">
                <table class="table align-middle table-hover">
                    <thead class="table-secondary">
                    <tr>
                        <th>#</th>
                        <Th width="50%">الأسم</Th>
                        <th>التحكم</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($content as $con)
                        <tr>
                            <td>{{$con->id}}</td>
                            <td>{{$con->name}}</td>
                            <td>
                                <div class="table-actions d-flex align-items-center gap-3 fs-6">
                                    <a href="{{route('admin.roles.edit', $con->id)}}" class="text-primary" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                       title="تعديل"><i class="bi bi-pencil-fill"></i></a>

                                    <a href="{{route('admin.roles.destroy', $con->id)}}" class="delete text-danger" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                       title="حذف"><i class="bi bi-trash-fill"></i></a>

                                </div>
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
