@extends('admin_dashboard.layout.master')
@section('Page_Title')  المحاضرات (الأوفلاين) @endsection

@section('content')


    <div class="card">
        <div class="card-body">

            <div class="d-flex align-items-center">
                <h5 class="mb-0"> <i class="bi bi-grid-fill"></i> المحاضرات الأوفلاين الخاصة بدورة <span
                        class="text-orange">{{ $course->title }}</span> </h5>
                <div class="ms-auto position-relative">
                    <a href="{{route('admin.courses.sessions.create', $course)}}" class="btnIcon btn btn-outline-primary px-5"><i class="lni lni-circle-plus"></i> إنشاء </a>
                </div>
            </div>

            <div class="table-responsive mt-4">

                <table class="table align-middle table-hover">
                    @foreach($content as $section)
                        <!-- Section Header Row -->
                        <tr style="background-color: #f0f0f0;">
                            <th colspan="6">{{ $section->name }}</th>
                        </tr>
                        <!-- Lectures under this Section -->
                        @forelse($section->sessions as $lecture)
                            <tr>
                                <td>{{ $lecture->id }}</td>
                                <td>{{ $lecture->title }}</td>
                                <td>{{ $lecture->session_date }}</td>
                                <td style="direction: ltr;text-align: right;">{{ date('H:i A', strtotime($lecture->time_from)) .' - '. date('H:i A', strtotime($lecture->time_to)) }}</td>
                                <td>{{ $lecture->location }}</td>
                                <td>
                                    <div class="table-actions d-flex align-items-center gap-3 fs-6">
                                        <a href="{{route('admin.courses.sessions.edit', ['course' => $course, 'session'=>$lecture])}}" class="text-primary" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                           title="تعديل"><i class="bi bi-pencil-fill"></i></a>

                                        <a href="{{route('admin.courses.sessions.destroy', ['course' => $course, 'session'=>$lecture])}}" class="delete text-danger" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                           title="حذف"><i class="bi bi-trash-fill"></i></a>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">لا يوجد محاضرات</td>
                            </tr>
                        @endforelse
                    @endforeach
                </table>
            </div>
        </div>
    </div>


@endsection

@push('js')
    @include('admin_dashboard.components.delete')
@endpush
