<div class="col-md-6">
    <label class="form-label">  العنوان </label>
    <input type="text" name="title" class="form-control" required value="{{$content->title}}">
</div>

<div class="col-md-12">
    <label class="form-label">  الوصف </label>
    <textarea class="form-control" cols="3" rows="3" name="body" required>{{$content->body}}</textarea>
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
    <select name="users[]" class="form-control form-select" multiple id="allUsersCodes"></select>

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



<div class="col-md-12 text-center  mx-auto my-5">
    <button type="submit" class="btnIcon btn btn-primary px-5">
        <i class="lni lni-circle-plus"></i>
        إرسال
    </button>
</div>

