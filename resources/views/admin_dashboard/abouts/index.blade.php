@extends('admin_dashboard.layout.master')

@section('Page_Title')  من نحن  @endsection

@section('breadcrumb')  من نحن  @endsection

@section('content')

    <div class="card">
        <div class="card-body">

            <div class="d-flex align-items-center">
                <h5 class="mb-0"> <i class="bi bi-grid-fill"></i>  من نحن </h5>
            </div>

            <div class="row g-3 mt-4">
                <div class="col-12">
                    <div class="card shadow-none bg-light border">
                        <div class="card-body">
                            <form class="row g-3" id="validateForm" method="post" enctype="multipart/form-data"
                                  action="{{route('admin.abouts.store')}}">
                                @csrf
                                <input type="hidden" value="{{$content?->id}}" name="id">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"> من نحن  </label>
                                    <textarea cols="3" rows="3" class="tiny" name="about_ar">{!! $content->about_ar !!}</textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"> مهمتنا</label>
                                    <textarea cols="3" rows="3" class="tiny" name="mission_ar">{!! $content->mission_ar !!}</textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"> رؤيتنا </label>
                                    <textarea cols="3" rows="3" class="tiny" name="vision_ar">{!! $content->vision_ar !!}</textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"> قيمنا  </label>
                                    <textarea cols="3" rows="3" class="tiny" name="goals_ar">{!! $content->goals_ar !!}</textarea>
                                </div>

                                <div class="col-md-12">
                                    @include('admin_dashboard.inputs.image',
                                    ['label_name' => 'الصورة' ,
                                    'input_name' => 'image',
                                    'required' => false,
                                    'accept'=>'images_only'
                                    ])
                                </div>


                                @if($content->id > 0)
                                    @include('admin_dashboard.inputs.edit_btn')
                                @else
                                    @include('admin_dashboard.inputs.add_btn')
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div><!--end row-->
        </div>
    </div>

@endsection
