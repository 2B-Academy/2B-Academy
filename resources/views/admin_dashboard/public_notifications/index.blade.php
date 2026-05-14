@extends('admin_dashboard.layout.master')
@section('Page_Title')  الإشعارات العامة @endsection

@section('content')


    <div class="card">
        <div class="card-body">

            <div class="d-flex align-items-center">
                <h5 class="mb-0"> <i class="bi bi-grid-fill"></i>  الإشعارات العامة </h5>
                <div class="ms-auto position-relative">
                    <a href="{{route('admin.notifications.create')}}" class="btnIcon btn btn-outline-primary px-5"><i class="lni lni-circle-plus"></i> إنشاء </a>
                </div>
            </div>

            <div class="table-responsive mt-4">
                <table class="table align-middle table-hover">
                    <thead class="table-secondary">
                    <tr>
                        <th>#</th>
                        <Th>العنوان</Th>
                        <th>الوصف</th>
                        <th>الحالة</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($content as $con)
                        <tr>
                            <td>{{$con->id}}</td>
                            <td>{{$con->title}}</td>
                            <td>{{$con->body}}</td>
                            <td>{{$con->for_public ? 'للجميع' : '-'}}</td>
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
