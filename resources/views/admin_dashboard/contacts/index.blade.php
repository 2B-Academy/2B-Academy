@extends('admin_dashboard.layout.master')
@section('Page_Title')  فورم تواصل معنا @endsection

@section('content')


    <div class="card">
        <div class="card-body">

            <div class="d-flex align-items-center">
                <h5 class="mb-0"> <i class="bi bi-grid-fill"></i>  تواصل معنا </h5>
            </div>

            <div class="table-responsive mt-4">
                <table class="table align-middle table-hover">
                    <thead class="table-secondary">
                    <tr>
                        <th>#</th>
                        <Th>الأسم</Th>
                        <th>البريد الإلكتروني</th>
                        <th>أنشي في</th>
                        <th>التحكم</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($content as $con)
                        <tr>
                            <td>{{$con->id}}</td>
                            <td>{{$con->name}}</td>
                            <td>{{$con->email}}</td>
                            <td>{{date('Y-m-d H:i A', strtotime($con->created_at))}}</td>
                            <td>
                                <div class="table-actions d-flex align-items-center gap-3 fs-6">
                                    <a href="{{route('admin.contacts.show', $con->id)}}" class="text-primary" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                       title="مشاهدة"><i class="lni lni-eye"></i></a>

                                    <a href="{{route('admin.contacts.destroy', $con->id)}}" class="delete text-danger" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                       title="حذف"><i class="bi bi-trash-fill"></i></a>

                                </div>
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
@endpush
