@extends('admin_dashboard.layout.master')
@section('Page_Title')   مستخدمين الدورات الأوفلاين | تعديل   @endsection

@section('content')

    <div class="row">
        <div class="col-lg-12 mx-auto">
            <div class="breadcrumb d-flex align-items-center justify-content-between">
                <div class="">
                    <a class="text-dark" href="{{route('admin.users-courses-offline.index')}}">مستخدمين الدورات الأوفلاين</a>
                    <span class="mx-2">-</span>
                    <strong class="text-primary">تعديل</strong>
                    <span class="mx-2">-</span>
                    <strong class="text-primary">{{$content->title}}</strong>
                </div>
            </div>
            <div class="card">
                <div class="card-body">

                    <div class="row g-3 mt-4">
                        <div class="col-12">
                            <div class="card shadow-none bg-light border">
                                <div class="card-body">
                                    <form class="row g-3" id="validateForm" method="post" enctype="multipart/form-data"
                                          action="{{route('admin.users-courses-offline.update', $content->id)}}">
                                        @method('put')
                                        @csrf
                                        @include('admin_dashboard.users-courses-offline._form')
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div><!--end row-->
                </div>
            </div>
        </div>
    </div>

@endsection
@push('js')
    <script>
        let selectedUsers = @json($selectedUsers);
        if (selectedUsers && selectedUsers.length) {
            selectedUsers.forEach(function(user) {
                let option = new Option(
                    user.machine_code+' - '+user.name+' - '+user.email+' - '+user.department_name,
                    user.id,
                    true,   // selected
                    true    // defaultSelected
                );
                $('#allUsers').append(option).trigger('change');
            });
        }
    </script>
@endpush
