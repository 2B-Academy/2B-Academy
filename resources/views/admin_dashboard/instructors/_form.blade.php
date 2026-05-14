<div class="col-md-6">
    <label class="form-label"> الأسم <small class="text-danger">*</small></label>
    <input type="text" name="name" class="form-control" required value="{{ $content->name }}">
</div>
<div class="col-md-12 mb-3">
    <label class="form-label"> نبذه <small class="text-danger">*</small></label>
    <textarea cols="3" rows="3" class="tiny" name="bio">{!! $content->bio !!}</textarea>
</div>

<div class="col-md-6">
    <label class="form-label"> البريد الإلكتروني </label>
    <input type="email" name="email" class="form-control" value="{{ $content->email }}" required>
</div>
<div class="col-md-6">
    <label class="form-label"> المسمي الوظيفي </label>
    <input type="text" name="job_title" class="form-control"
        value="{{ $content->job_title }}" required>
</div>


<div class="col-md-12">
    @include('admin_dashboard.inputs.image', [
        'label_name' => 'الصورة',
        'input_name' => 'image',
        'required' => true,
        'accept' => 'images_only',
    ])
</div>



@if ($content->id > 0)
    @include('admin_dashboard.inputs.edit_btn')
@else
    @include('admin_dashboard.inputs.add_btn')
@endif
