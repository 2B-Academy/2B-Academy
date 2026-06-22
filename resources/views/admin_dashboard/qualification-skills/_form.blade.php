<div class="col-md-6">
    <label class="form-label">{{ __('text.name_ar') }} <small class="text-danger">*</small></label>
    <input type="text" name="name[ar]" class="form-control" required
           value="{{ old('name.ar', $content->getTranslation('name', 'ar', false)) }}">
</div>

<div class="col-md-6">
    <label class="form-label">{{ __('text.name_en') }} <small class="text-danger">*</small></label>
    <input type="text" name="name[en]" class="form-control" required
           value="{{ old('name.en', $content->getTranslation('name', 'en', false)) }}">
</div>

@if ($content->id > 0)
    @include('admin_dashboard.inputs.edit_btn')
@else
    @include('admin_dashboard.inputs.add_btn')
@endif
