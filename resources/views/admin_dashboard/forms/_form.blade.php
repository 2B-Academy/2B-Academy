<div class="col-md-12">
    <label class="form-label">  عنوان الأختبار </label>
    <input type="text" name="title" class="form-control" required value="{{$content->title}}">
</div>
<div class="col-md-6">
    <label class="form-label">  المدة الزمنية للأختبار (عدد الدقائق) </label>
    <input type="number" min="0" name="duration" class="form-control" placeholder="60" required value="{{$content->duration}}">
</div>
<div class="col-md-6">
    <label class="form-label">  الدرجة النهائية للأختبار </label>
    <input type="number" min="0" name="full_mark" class="form-control" placeholder="100" required value="{{$content->full_mark}}">
</div>


<div class="col-md-4">
    <label class="form-check-label" for="active">الحالة</label>
    <div class="form-check form-switch mt-2">
        <input class="form-check-input customSliderCheckbox" type="checkbox"
               name="active" checked value="1" id="active" @checked($content->active)>
    </div>
</div>



@if($content->id > 0)
    @include('admin_dashboard.inputs.edit_btn')
@else
    @include('admin_dashboard.inputs.add_btn')
@endif
