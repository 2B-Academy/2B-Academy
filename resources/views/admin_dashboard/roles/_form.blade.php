<div class="col-md-6">
    <label class="form-label">  الأسم  <span class="text-danger">*</span> </label>
    <input type="text" name="name" class="form-control" required  value="{{$content->name}}">
</div>
<div class="col-md-12">
    <div class="d-flex align-items-center">
        <input type="checkbox" class="roleCheck mx-2" id="checkAll">
        <label class="form-label mb-0" for="checkAll">  تحديد الكل  </label>
    </div>
</div>
<div class="col-12">
    @foreach ($pages as $table_name => $all)
        <h6 class="w-100 py-3 px-4 mb-0 tableName"><strong>{{ __('text.'.strtolower($table_name)) }}</strong></h6>
        <div class="pages py-3 px-4">
            @foreach($all as $page)
                <label class="d-flex align-items-center mb-2">
                    <input type="checkbox" class="roleCheck" name="pages[]" value="{{ $table_name.'-'.$page }}"
                        {{ $content->hasPermissionTo($table_name.'-'.$page) ? 'checked' : '' }}>
                    {{ ucfirst($table_name.'-'.$page) }}
                </label>
            @endforeach
        </div>
    @endforeach
</div>

@if($content->id > 0)
    @include('admin_dashboard.inputs.edit_btn')
@else
    @include('admin_dashboard.inputs.add_btn')
@endif
