@extends('admin_dashboard.layout.master')
@section('Page_Title')  الأختبارات العامة @endsection

@section('content')


    <div class="card">
        <div class="card-body">

            <div class="d-flex align-items-center">
                <h5 class="mb-0"> <i class="bi bi-grid-fill"></i>  الأختبارات العامة </h5>
                <div class="ms-auto position-relative">
                    <a href="{{route('admin.forms.create')}}" class="btnIcon btn btn-outline-primary px-5"><i class="lni lni-circle-plus"></i> إنشاء </a>
                </div>
            </div>

            <div class="table-responsive mt-4">
                <table class="table align-middle table-hover">
                    <thead class="table-secondary">
                    <tr>
                        <th>#</th>
                        <Th>عنوان الأختبار</Th>
                        <Th>المدة الزمنية</Th>
                        <Th>الدرجة النهائية</Th>
                        <th>الحالة</th>
                        <th>التحكم</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($content as $con)
                        <tr>
                            <td>{{$con->id}}</td>
                            <td>{{$con->title}}</td>
                            <td>{{$con->duration}} دقيقة</td>
                            <td>{{$con->full_mark}}</td>
                            <td>
                                <strong class="mx-2 badge @if($con->active) bg-light-success text-success @else bg-light-danger text-danger @endif">{{$con->active ? 'نشط' : 'غير نشط'}}</strong>
                            </td>
                            <td>
                                <div class="table-actions d-flex align-items-center gap-3 fs-6">
                                    <button id="copyLink" onclick="copyLink('{{route('front.forms.start', $con->uuid)}}')"
                                            class="btn btn-outline-dark" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                       title="نسخ الرابط"><i class="bx bx-copy"></i></button>

                                    <a href="{{route('admin.forms.edit', $con->id)}}" class="btn btn-success" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                       title=""><i class="bx bx-edit"></i> الأسئلة </a>

                                    <a href="{{route('admin.forms.show', $con->id)}}" class="btn btn-primary" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                       title=""><i class="lni lni-eye"></i> الدرجات </a>

                                    <a href="{{route('admin.forms.destroy', $con->id)}}" class="delete text-danger" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                       title="حذف"><i class="bi bi-trash-fill"></i></a>

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
                    {{$content->links()}}
                </div>
            </div>
        </div>
    </div>


@endsection

@push('js')
    @include('admin_dashboard.components.delete')
    <script>
        function copyLink(url) {
            navigator.clipboard.writeText(url);
            swal({
                title: "تم نسخ الرابط.",
                text: '',
                icon: "success",
            })
        }
    </script>
@endpush
