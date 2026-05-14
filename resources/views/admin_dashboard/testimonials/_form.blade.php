<div class="col-md-6">
    <label class="form-label">  الأسم  </label>
    <input type="text" name="name_ar" class="form-control"  required value="{{$content->name_ar}}">
</div>

<div class="col-md-12 mb-3">
    <label class="form-label"> الوصف </label>
    <textarea cols="3" rows="3" class="tiny" name="description_ar">{!! $content->description_ar !!}</textarea>
</div>

<div class="col-md-12">
    @include('admin_dashboard.inputs.image',
    ['label_name' => 'الصورة' ,
    'input_name' => 'image',
    'required' => true,
    'accept'=>'images_only'
    ])
</div>
<div class="col-md-4">
    <label class="form-check-label" for="active">الحالة</label>
    <div class="form-check form-switch mt-2">
        <input class="form-check-input customSliderCheckbox" type="checkbox"
               name="active" value="1" id="active" @checked($content->active)>
    </div>
</div>


@if($content->id > 0)
    @include('admin_dashboard.inputs.edit_btn')
@else
    @include('admin_dashboard.inputs.add_btn')
@endif
