<div class="col-md-6">
    <label class="form-label"> <small class="text-danger">*</small> الأسم </label>
    <input type="text" name="name" class="form-control" required value="{{$content->name}}">
</div>


@if($content->id > 0)
    @include('admin_dashboard.inputs.edit_btn')
@else
    @include('admin_dashboard.inputs.add_btn')
@endif
