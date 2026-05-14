@extends('admin_dashboard.layout.master')
@section('Page_Title')   مهام الموظفين | اضافة تقييم   @endsection

@section('content')

    <div class="row">
        <div class="col-lg-12 mx-auto">
            <div class="breadcrumb d-flex align-items-center justify-content-between">
                <div class="">
                    <a class="text-dark" href="{{route('admin.users-courses-assignments.index')}}">مهام الموظفين</a>
                    <span class="mx-2">-</span>
                    <strong class="text-primary">اضافة تقييم</strong>
                    <span class="mx-2">-</span>
                    <strong class="text-primary">{{$content->assignment?->title}}</strong>
                </div>
            </div>
            <div class="card">
                <div class="card-body">

                    <div class="row g-3 mt-4">
                        <div class="col-12">
                            <div class="card shadow-none bg-light border">
                                <div class="card-body">
                                    <div class="row align-items-center mb-5 bg-dark p-3 rounded text-center">
                                        <div class="col-md-6">
                                            <a href="{{$content->getFileUrl($content->assignment?->file)}}" download class="btn btn-sm btn-secondary">
                                                <i class="lni lni-download mx-1"></i> ملف المهام
                                            </a>
                                        </div>
                                        <div class="col-md-6">
                                            <a href="{{$content->getFileUrl($content->user_file)}}" download class="btn btn-sm btn-main">
                                                <i class="lni lni-download mx-1"></i> ملف الموظف
                                            </a>
                                        </div>
                                    </div>
                                    <form class="row g-3" id="validateForm" method="post" enctype="multipart/form-data"
                                          action="{{route('admin.users-courses-assignments.update', $content->id)}}">
                                        @method('put')
                                        @csrf
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label"> Feedback <small class="text-danger">*</small></label>
                                            <textarea cols="3" rows="3" class="form-control" required name="feedback">{{$content->feedback}}</textarea>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label"> Score (percentage %) </label>
                                            <div class="d-flex align-items-center justify-content-start">
                                                <input type="number" name="score" class="form-control" value="{{$content->score}}" min="0" max="100">
                                                <span class="bg-dark px-3 py-2 rounded text-white">%</span>
                                            </div>
                                        </div>
                                        <div class="col-md-12 text-center  mx-auto mt-5">
                                            <button type="submit" class="btnIcon btn btn-success px-5">
                                                <i class="bx bx-edit-alt"></i>
                                                اضف تقييم
                                            </button>
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
