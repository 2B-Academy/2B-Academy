<div class="col-md-12">
    <label class="form-label"> اختر الدورة <small class="text-danger">*</small></label>
    <select  name="course_id" required id="course_id" class="form-control form-select {{$content->id ? '' : 'select2'}}" @if($content->id) disabled @endif>
        <option value="">اختر الدورة</option>
        @foreach($courses as $course)
            <option value="{{ $course->id }}" @selected($content->id == $course->id) >{{ $course->title }}</option>
        @endforeach
    </select>
</div>

<div class="col-md-12">
    <label class="form-label"> اختر المجموعة <small class="text-danger">*</small></label>
    <select name="group_id" id="group_id" required class="form-control form-select {{$content->id ? '' : 'select2'}}" @if($content->id) disabled @endif>
        @if($content->id > 0)
            <option value="{{$selectedGroup?->id}}">{{$selectedGroup?->name}}</option>
        @endif
    </select>
</div>


<div class="col-md-12">
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
