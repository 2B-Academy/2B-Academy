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
    <label class="form-label"> عنوان المحاضرة <small class="text-danger">*</small></label>
    <input type="text" name="title" class="form-control" required value="{{ $content->title }}">
</div>
<div class="col-md-12" style="visibility: hidden">
    <label class="form-label"> نوع فيديو المحاضرة <small class="text-danger">*</small></label>
    <select name="type" required class="form-control form-select" id="type">
        <option value="url" @selected($content->type == 'url')>رابط خارجي</option>
        <option value="upload" @selected($content->type == 'upload')>اختيار من فولدر (storage/app/public/CourseLecture/)</option>
    </select>
</div>

<div id="urlDiv" class="my-3" @if($content->type != 'upload') style="display:none;" @endif>
    <button class="btn btn-warning" type="button"
            id="chooseFileBtn">اختر فيديو</button>
</div>
<div class="col-md-12"  id="uploadDiv">
    <label class="form-label"> فيديو المحاضرة <small class="text-danger">*</small></label>
    <input type="url" name="video" id="videoUpload" class="form-control" required value="{{ $content->video }}">
</div>

<!-- Modal -->
<div class="modal fade" id="fileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5>اختر فيديو المحاضرة من فولدر (storage/app/public/CourseLecture)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="fileList">
                <!-- Files will be loaded here -->
                <p class="alert alert-danger">لا يوجد فيديوهات</p>
            </div>
        </div>
    </div>
</div>



@if($content->id > 0)
    @include('admin_dashboard.inputs.edit_btn')
@else
    @include('admin_dashboard.inputs.add_btn')
@endif
