<div class="col-md-6">
    <label class="form-label"> السيكشن <small class="text-danger">*</small></label>
    <select name="section_id" required class="form-control form-select">
        <option value="">اختر السيكشن</option>
        @foreach ($course->sections as $section)
            <option @selected($content->section_id == $section->id) value="{{ $section->id }}">{{ $section->name }}</option>
        @endforeach
    </select>
</div>
<div class="col-md-6">
    <label class="form-label"> عنوان الأختبار <small class="text-danger">*</small></label>
    <input type="text" name="title" class="form-control" required value="{{ $content->title }}">
</div>
<div class="col-md-6">
    <label class="form-label"> درجة الأختبار <small class="text-danger">*</small></label>
    <input type="number" min="0" name="degree" class="form-control" required value="{{ $content->degree }}">
</div>
<div class="col-md-6">
    <label class="form-check-label" for="active">الأختبار نهائي ؟</label>
    <div class="form-check form-switch mt-2">
        <input class="form-check-input customSliderCheckbox" type="checkbox" name="is_final" value="1"
               id="is_final" @checked($content->is_final)>
    </div>
</div>


@include('admin_dashboard.courses.exams._questions')

@if($content->id > 0)
    @include('admin_dashboard.inputs.edit_btn')
@else
    @include('admin_dashboard.inputs.add_btn')
@endif
