@extends('admin_dashboard.layout.master')
@section('Page_Title')   الموظفين | إنشاء   @endsection

@section('content')

    <div class="row">
        <div class="col-lg-12 mx-auto">
            <div class="breadcrumb d-flex align-items-center justify-content-between">
                <div class="">
                    <a class="text-dark" href="{{route('admin.users.index')}}">الموظفين</a>
                    <span class="mx-2">-</span>
                    <strong class="text-primary">إنشاء</strong>
                </div>
            </div>
            <div class="card">
                <div class="card-body">

                    <div class="row g-3 mt-4">
                        <div class="col-12">
                            <div class="card shadow-none bg-light border">
                                <div class="card-body">
                                    <form class="row g-3" id="validateForm" method="post" enctype="multipart/form-data"
                                          action="{{route('admin.users.store')}}">
                                        @csrf
                                        <div class="col-md-6">
                                            <label class="form-label">  الأسم <small class="text-danger">*</small> </label>
                                            <input type="text" name="name" class="form-control" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">  الهاتف </label>
                                            <input type="number" min="0" name="phone" class="form-control">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">  القسم <small class="text-danger">*</small> </label>
                                            <input type="text" name="department_name" class="form-control" required>
                                        </div>

                                        @include('admin_dashboard.inputs.add_btn')


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
        $(document).on('change', '#for_public', function (){
            if ($(this).is(':checked')) {
                $('#users-container').addClass('d-none');
            } else {
                $('#users-container').removeClass('d-none');
            }
        });
    </script>
@endpush
