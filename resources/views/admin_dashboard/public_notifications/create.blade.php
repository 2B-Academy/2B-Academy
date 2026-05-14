@extends('admin_dashboard.layout.master')
@section('Page_Title')   الإشعارات العامة | إنشاء   @endsection

@section('content')

    <div class="row">
        <div class="col-lg-12 mx-auto">
            <div class="breadcrumb d-flex align-items-center justify-content-between">
                <div class="">
                    <a class="text-dark" href="{{route('admin.notifications.index')}}">الإشعارات العامة</a>
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
                                          action="{{route('admin.notifications.store')}}">
                                        @csrf
                                        @include('admin_dashboard.public_notifications._form')
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
