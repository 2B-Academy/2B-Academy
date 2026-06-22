<div class="col-md-12">
    <label class="form-label"> نوع الدورة <small class="text-danger">*</small></label>
    <select @if($content->id > 0) disabled @else name="course_type" required @endif class="form-control form-select">
        <option value="online" @selected($content->course_type == 'online')>أونلاين 🟢</option>
        <option value="offline" @selected($content->course_type == 'offline')>أوفلاين 🔴</option>
    </select>
</div>

<div class="col-md-4">
    <label class="form-label"> القسم <small class="text-danger">*</small></label>
    <select name="category_id" required class="form-control form-select">
        <option value="">اختر القسم</option>
        @foreach ($categories as $category)
            <option @selected($content->category_id == $category->id) value="{{ $category->id }}">{{ $category->name }}</option>
        @endforeach
    </select>
</div>
<div class="col-md-4">
    <label class="form-label"> الأسم <small class="text-danger">*</small></label>
    <input type="text" name="title" class="form-control" required value="{{ $content->title }}">
</div>
<div class="col-md-4">
    <label class="form-label"> الأسم للشهادة </label>
    <input type="text" name="title_for_certificate" class="form-control" required value="{{ $content->title_for_certificate }}">
</div>


<div class="col-md-12">
    <label class="form-label"> المحاضرين <small class="text-danger">*</small></label>
    <select name="instructors[]" required class="form-control form-select select2" multiple>
        <option value="">اختر المحاضرين</option>
        @foreach($instructors as $instructor)
            <option value="{{ $instructor->id }}" @selected(($content->id) ? in_array($instructor->id, $selectedInstructorsIds): '')>{{ $instructor->name . ' - '. $instructor->email }}</option>
        @endforeach
    </select>
</div>

<div class="col-md-12">
    <label class="form-label"> {{ __('text.qualification_skills') }} </label>
    <select name="qualification_skill_ids[]" class="form-control form-select select2" multiple>
        @foreach($qualificationSkills as $skill)
            <option value="{{ $skill->id }}"
                @selected(in_array($skill->id, old('qualification_skill_ids', $selectedQualificationSkillIds ?? [])))
            >{{ $skill->name }}</option>
        @endforeach
    </select>
</div>

<div class="col-md-12 mb-3">
    <label class="form-label"> الوصف <small class="text-danger">*</small></label>
    <textarea cols="3" rows="3" class="tiny" name="description">{!! $content->description !!}</textarea>
</div>

<div class="col-md-6">
    <label class="form-label"> السعر </label>
    <input type="text" name="price" class="form-control" value="{{ $content->price }}">
</div>
<div class="col-md-6">
    <label class="form-label"> العملة </label>
    <input type="text" name="currency" class="form-control" placeholder="ج.م | دولار | ر.س ......."
        value="{{ $content->currency }}">
</div>

<div class="col-md-6">
    <label class="form-label"> عدد الساعات <small class="text-danger">*</small> </label>
    <input type="number" min="0" name="hours" class="form-control" value="{{ $content->hours }}" required>
</div>

<div class="col-md-6">
    <label class="form-label"> اللغه </label>
    <input type="text" name="language" class="form-control" placeholder="عربي | انجليزي | فرنساوي ........"
        value="{{ $content->language }}">
</div>

<div class="col-md-6">
    <label class="form-label"> المستوي <small class="text-danger">*</small></label>
    <select name="level" required class="form-control form-select">
        <option value="beginner" @selected($content->level == 'beginner')>مبتدئ</option>
        <option value="medium" @selected($content->level == 'medium')>متوسط</option>
        <option value="professional" @selected($content->level == 'professional')>محترف</option>
    </select>
</div>

<div class="col-md-6">
    <label class="form-label"> هل يوجد شهادة في نهاية الدورة ؟ <small class="text-danger">*</small></label>
    <select name="certificate" required class="form-control form-select">
        <option value="1" @selected($content->certificate == '1')>نعم</option>
        <option value="0" @selected($content->certificate == '0')>لا</option>
    </select>
</div>


{{--<div class="col-md-12">--}}
{{--    <label class="form-label">رابط مقدمة الدورة</label>--}}
{{--    <input type="text" name="intro_video" class="form-control" value="{{ $content->intro_video }}">--}}
{{--</div>--}}

<div class="col-md-12">
    @include('admin_dashboard.inputs.image', [
        'label_name' => 'الصورة',
        'input_name' => 'image',
        'required' => true,
        'accept' => 'images_only',
    ])
</div>

<div class="col-md-4">
    <label class="form-check-label" for="outside_materials">الدورة تحتوي علي فيديوهات خارجيه (يوتيوب ...)  </label>
    <div class="form-check form-switch mt-2">
        <input class="form-check-input customSliderCheckbox" type="checkbox" name="outside_materials" value="1"
            id="outside_materials" @checked($content->outside_materials)>
    </div>
</div>


<div class="col-md-4">
    <label class="form-check-label" for="active">الحالة</label>
    <div class="form-check form-switch mt-2">
        <input class="form-check-input customSliderCheckbox" type="checkbox" name="active" value="1"
               id="active" @checked($content->active)>
    </div>
</div>


<div class="col-md-4">
    <label class="form-check-label" for="is_evaluate"> التقييم  </label>
    <div class="form-check form-switch mt-2">
        <input class="form-check-input customSliderCheckbox" type="checkbox" name="is_evaluate" value="1"
               id="is_evaluate" @checked($content->is_evaluate)>
    </div>
</div>

<div class="col-md-4">
    <label class="form-check-label" for="allow_attendances"> الموظف يأخذ الحضور بنفسه  </label>
    <div class="form-check form-switch mt-2">
        <input class="form-check-input customSliderCheckbox" type="checkbox" name="allow_attendances" value="1"
               id="allow_attendances" @checked($content->allow_attendances)>
    </div>
</div>

@if ($content->id > 0)
    @include('admin_dashboard.inputs.edit_btn')
@else
    @include('admin_dashboard.inputs.add_btn')
@endif
