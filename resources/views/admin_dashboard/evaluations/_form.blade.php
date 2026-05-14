
<div class="col-md-12">
    <label class="form-label"> القسم <small class="text-danger">*</small></label>
    <select name="evaluation_category_id" required class="form-control form-select">
        <option value="">اختر القسم</option>
        @foreach ($categories as $category)
            <option @selected($content->evaluation_category_id == $category->id) value="{{ $category->id }}">{{ $category->name }}</option>
        @endforeach
    </select>
</div>
<div class="col-md-12">
    <label class="form-label">  السؤال <small class="text-danger">*</small></label>
    <input type="text" name="title" class="form-control" required value="{{$content->title}}">
</div>
<div class="col-md-12">
    <label class="form-label"> نوع السؤال <small class="text-danger">*</small></label>
    <select name="type" required class="form-control form-select">
        <option @selected($content->type == 'text') value="text">Text</option>
        <option @selected($content->type == 'five') value="five">Rate from 1 to 5 (Stars)</option>
        <option @selected($content->type == 'ten') value="ten">Rate from 1 to 10</option>
    </select>
</div>

{{--<div class="col-md-12">--}}
{{--    <label class="form-check-label" for="active">مطلوب (*)</label>--}}
{{--    <div class="form-check form-switch mt-2">--}}
{{--        <input class="form-check-input customSliderCheckbox" type="checkbox" name="is_required" value="1"--}}
{{--               id="is_required" @if($content->id > 0) @checked($content->is_required) @else checked @endif>--}}
{{--    </div>--}}
{{--</div>--}}

@if($content->id > 0)
    @include('admin_dashboard.inputs.edit_btn')
@else
    @include('admin_dashboard.inputs.add_btn')
@endif
