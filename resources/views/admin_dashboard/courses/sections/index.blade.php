@extends('admin_dashboard.layout.master')
@section('Page_Title')
    @if($course->course_type == 'offline')
        المجموعات
    @else
        السكاشن
    @endif

@endsection

@section('content')
    <div class="card">
        <div class="card-body">

            <div class="d-flex align-items-center">
                <h5 class="mb-0"> <i class="bi bi-grid-fill"></i>
                    @if($course->course_type == 'offline')
                        المجموعات
                    @else
                        السكاشن
                    @endif الخاصة بدورة  <span class="text-orange">{{ $course->title }}</span> </h5>
            </div>


            <div class="card">
                <div class="card-body">

                    <div class="row g-3 mt-4">
                        @if (count($content) > 0)
                            <div class="col-12 text-end">
                                <a href="{{ route('admin.courses.sections.destroyAll', $course) }}"
                                    class="delete btn btn-outline-danger"><i class="mx-1 lni lni-trash"></i> حذف كل @if($course->course_type == 'offline')
                                        المجموعات
                                    @else
                                        السكاشن
                                    @endif</a>
                            </div>
                        @endif
                        <div class="col-12">
                            <div class="card shadow-none bg-light border">
                                <div class="card-body">
                                    <form class="row g-3" id="validateForm" method="post" enctype="multipart/form-data"
                                        action="{{ route('admin.courses.sections.store', $course) }}">
                                        @csrf
                                        <div class="row">
                                            <div class="col-12 py-3">
                                                <button type="button" class="btn btn-sm btn-icon btn-success"
                                                    id="addNew"><i class="lni lni-plus"></i></button>
                                            </div>
                                            <div class="row" id="rows">
                                                @if (count($content) > 0)
                                                    @foreach ($content as $con)
                                                        <div class="col-12 mb-3" id="row">
                                                            <input type="hidden" name="section_id[]"
                                                                value="{{ $con->id }}">
                                                            <div
                                                                class="d-flex align-items-center justify-content-around gap-4">
                                                                <input type="text" class="form-control" name="name[]"
                                                                    placeholder="الأسم" value="{{ $con->name }}"
                                                                    required>
                                                                <button type="button" id="removeRow"
                                                                    class="btn btn-sm btn-icon btn-danger">
                                                                    <i class="lni lni-close"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="col-12 mb-3" id="row">
                                                        <div class="d-flex align-items-center justify-content-around gap-4">
                                                            <input type="text" class="form-control" name="name[]"
                                                                placeholder="الأسم" value="" required>
                                                            <button type="button" id="removeRow"
                                                                class="btn btn-sm btn-icon btn-danger">
                                                                <i class="lni lni-close"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-12 py-3">
                                                <button type="submit" class="btn btn-main">حفظ</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div><!--end row-->
                </div>
            </div>




        </div>
    </div>
@endsection

@push('js')
    @include('admin_dashboard.components.deleteAll')
@endpush
