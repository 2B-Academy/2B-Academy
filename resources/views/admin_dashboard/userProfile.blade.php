@extends('admin_dashboard.layout.master')
@section('Page_Title')   لوحة التحكم | بياناتي   @endsection


@section('content')

    <div class="row">
        <div class="col-lg-12 mx-auto">
            <div class="breadcrumb d-flex align-items-center justify-content-between">
            </div>
            <div class="card">
                <div class="card-body">

                    <div class="row g-3 mt-4">
                        <div class="col-12">
                            <div class="card shadow-none bg-light border">
                                <div class="card-body">
                                    <form class="row g-3" id="validateForm" method="post" enctype="multipart/form-data"
                                          action="{{route('admin.updateUserProfile')}}">
                                        @method('put')
                                        @csrf

                                        <div class="col-12 mb-3">
                                            <div class="form-group">
                                                <label for="name"> الأسم <span class="text-danger">*</span> </label>
                                                <input type="text" name="name" value="{{auth()->user()->name}}" id="name" class="form-control" required  placeholder="الأسم">
                                            </div>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <div class="form-group">
                                                <label for="email"> البريد الإلكتروني <span class="text-danger">*</span> </label>
                                                <input type="email" value="{{auth()->user()->email}}" name="email" id="email" class="form-control" required  placeholder="البريد الإلكتروني">
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <a href="#" id="togglePassword" class="text-decoration-underline" >تغيير كلمة المرور ؟</a>
                                        </div>
                                        <div class="col-12 mb-3 d-none" id="changePassword">
                                            <div class="form-group">
                                                <label for="password"> كلمة المرور الجديدة </label>
                                                <input type="text"  name="password" id="password" class="form-control"  placeholder="كلمة المرور الجديدة">
                                            </div>
                                        </div>
                                        @include('admin_dashboard.inputs.edit_btn')
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


        $(document).on('click', '#togglePassword', function(e){
            e.preventDefault();
            $('#changePassword').toggleClass('d-none');
        });

    </script>



@endpush
