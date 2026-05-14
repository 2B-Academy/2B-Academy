@extends('admin_dashboard.layout.master')
@section('Page_Title')   الأدوار | إنشاء   @endsection
<style>
    .tableName
    {
        background: #e4e4e4;
        border-radius: 5px;
    }
    .roleCheck
    {
        width: 25px;
        height: 25px;
        margin: 0 10px;
    }

</style>
@section('content')

    <div class="row">
        <div class="col-lg-12 mx-auto">
            <div class="breadcrumb d-flex align-items-center justify-content-between">
                <div class="">
                    <a class="text-dark" href="{{route('admin.roles.index')}}">الأدوار</a>
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
                                          action="{{route('admin.roles.store')}}">
                                        @csrf
                                        @include('admin_dashboard.roles._form')
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
        $(document).on('change', '#checkAll',function(){
            var isChecked = $(this).is(':checked');
            $('.roleCheck').not(this).prop('checked', isChecked);
        });
    </script>
@endpush
