<div class="col-md-12">
    <label class="form-label"> المجموعة <small class="text-danger">*</small></label>
    <select name="section_id" required class="form-control form-select">
        <option value="">اختر المجموعة</option>
        @foreach ($course->sections as $section)
            <option @selected($content->section_id == $section->id) value="{{ $section->id }}">{{ $section->name }}</option>
        @endforeach
    </select>
</div>


<div class="col-md-6">
    <label class="form-label"> الوقت من <small class="text-danger">*</small></label>
    <input type="time" name="time_from" class="form-control" required value="{{ date('H:i', strtotime($content->time_from)) }}">
</div>
<div class="col-md-6">
    <label class="form-label"> الوقت إلي <small class="text-danger">*</small></label>
    <input type="time" name="time_to" class="form-control" required value="{{ date('H:i', strtotime($content->time_to)) }}">
</div>

<div class="col-md-12">
    <label class="form-label"> اللوكيشن <small class="text-danger">*</small></label>
    <input type="text" name="location" class="form-control" required value="{{ $content->location }}">
</div>


@if($content->id > 0)
    <div class="col-md-6">
        <label class="form-label"> الأسم <small class="text-danger">*</small></label>
        <input type="text" name="title" class="form-control" placeholder="الأسم" value="{{$content->title}}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label"> تاريخ المحاضرة <small class="text-danger">*</small></label>
        <input type="date" name="session_date" class="form-control" required value="{{$content->session_date}}">
    </div>

@else
    <div class="col-12 py-3">
        <button type="button" class="btn btn-sm btn-icon btn-success"
                id="addNew"><i class="lni lni-plus"></i></button>
    </div>
    <div class="row" id="rows">
        <div class="col-12 mb-3" id="row">
            <div class="d-flex align-items-center justify-content-around gap-4">
                <input type="text" name="title[]" class="form-control" placeholder="الأسم" required>
                <input type="date" name="session_date[]" class="form-control" required>
                <button type="button" id="removeRow"
                        class="btn btn-sm btn-icon btn-danger">
                    <i class="lni lni-close"></i>
                </button>
            </div>
        </div>
    </div>
@endif



@if($content->id > 0)
    @include('admin_dashboard.inputs.edit_btn')
@else
    @include('admin_dashboard.inputs.add_btn')
@endif
