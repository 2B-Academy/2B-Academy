<div class="col-md-12">
    <label class="form-label"> اختر الدورة <small class="text-danger">*</small></label>
    <select  name="course_id" required class="form-control form-select {{$content->id ? '' : 'select2'}}" @if($content->id) disabled @endif>
        <option value="">اختر الدورة</option>
        @foreach($courses as $course)
            <option value="{{ $course->id }}" @selected($content->id == $course->id) >{{ $course->title }}</option>
        @endforeach
    </select>
</div>

<div class="col-md-12">
    <label class="form-check-label" for="active">للجميع</label>
    <div class="form-check form-switch mt-2">
        <input class="form-check-input customSliderCheckbox" type="checkbox" name="for_public" value="1"
               id="for_public" @checked($content->for_public)>
    </div>
</div>


<div class="col-md-12 {{$content->for_public ? 'd-none' : ''}}" id="users-container">
    <label class="form-label"> الموظفين <small class="text-danger">*</small></label>
    <select name="users[]" class="form-control form-select" multiple id="allUsers"></select>
   @if(!$content->id)
        <br>
        <hr>
        أو
        <hr>
        <div class="my-2">
            <label class="form-label"> يمكنك رفع ملف اكسيل شيت بأرقام الموظفين  <a href="{{asset('admin_dashboard/assets/images/users_example_test.xlsx')}}" download target="_blank"> مثال تيتست </a> </label>
            <input type="file" name="users_sheet" accept=".xlsx" class="form-control">
        </div>
   @endif
</div>


<hr>
<div class="col-md-12">
    <label class="form-label"> نص الإشعار </label>
    <textarea cols="3" rows="3" class="form-control" name="notification_text">{{$content->notification_text}}</textarea>
</div>

@if($content->id > 0)
    @include('admin_dashboard.inputs.edit_btn')
@else
    @include('admin_dashboard.inputs.add_btn')
@endif
