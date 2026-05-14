<div class="col-md-6">
    <label class="form-label">  الأسم  <span class="text-danger">*</span> </label>
    <input type="text" name="name" class="form-control" required  value="{{$content->name}}">
</div>
<div class="col-md-6">
    <label class="form-label">  البريد الإلكتروني  <span class="text-danger">*</span> </label>
    <input type="email" name="email" class="form-control" required  value="{{$content->email}}">
</div>
<div class="col-md-6">
    <label class="form-label">  كلمة المرور  </label>
    <input type="text" name="password" class="form-control">
</div>
<div class="col-md-6">
    <label class="form-label">  تأكيد كلمة المرور  </label>
    <input type="text" name="password_confirmation" class="form-control">
</div>
<div class="col-md-12">
    <label class="form-label"> الدور  <span class="text-danger">*</span>  </label>
    <select class="form-control form-select" required name="role">
        <option value="">--اختر--</option>
        @foreach($roles as $role)
            <option value="{{$role->name }}"  {{ $content->hasRole($role->name) ? 'selected' : '' }}>{{$role->name }}</option>
        @endforeach
    </select>
</div>
@if($content->id > 0)
    @include('admin_dashboard.inputs.edit_btn')
@else
    @include('admin_dashboard.inputs.add_btn')
@endif
