@extends('admin_dashboard.layout.master')
@section('Page_Title')
    المهام
@endsection

@section('content')
    <div class="card">
        <div class="card-body">

            <div class="d-flex align-items-center">
                <h5 class="mb-0"> <i class="bi bi-grid-fill"></i> المهام الخاصة بدورة <span
                        class="text-orange">{{ $course->title }}</span> </h5>
            </div>


            <div class="card">
                <div class="card-body">

                    <div class="row g-3 mt-4">
                    @if (count($content) > 0)
                        <div class="col-12 text-end">
                            <a href="{{ route('admin.courses.assignments.destroyAll', $course) }}"
                                class="delete btn btn-outline-danger"><i class="mx-1 lni lni-trash"></i> حذف كل المهام</a>
                        </div>
                    @endif

                        <div class="col-12">
                            <div class="card shadow-none bg-light border">
                                <div class="card-body">
                                    <form class="row g-3" id="validateForm" method="post" enctype="multipart/form-data"
                                        action="{{ route('admin.courses.assignments.store', $course) }}">
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
                                                            <input type="hidden" name="assignment_id[]"
                                                                value="{{ $con->id }}">
                                                            <div
                                                                class="d-flex align-items-center justify-content-around gap-4">
                                                                <div class="input w-100">
                                                                    <label>العنوان</label>
                                                                    <input type="text" class="form-control"
                                                                        name="title[]" placeholder="العنوان"
                                                                        value="{{ $con->title }}" required>
                                                                </div>
                                                                <div class="input w-100">
                                                                    <label>الملف <small
                                                                            class="text-danger"></small>
                                                                        @if ($con->file)
                                                                            <a href="{{ $con->getFileUrl($con->file) }}"
                                                                                class="text-decoration-underline">تحميل</a>
                                                                        @endif
                                                                    </label>
                                                                    <input type="file" class="form-control"
                                                                        name="file[]">
                                                                </div>
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
                                                            <div class="input w-100">
                                                                <label>العنوان</label>
                                                                <input type="text" class="form-control" name="title[]"
                                                                    placeholder="العنوان" value="" required>
                                                            </div>
                                                            <div class="input w-100">
                                                                <label>الملف <small
                                                                        class="text-danger">(pdf only)</small></label>
                                                                <input type="file" class="form-control" name="file[]">
                                                            </div>
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
