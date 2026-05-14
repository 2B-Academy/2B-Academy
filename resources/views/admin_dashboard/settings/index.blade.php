@extends('admin_dashboard.layout.master')

@section('Page_Title')  ضبط الإعدادات  @endsection


@section('content')

    <div class="card">
        <div class="card-body">

            <div class="d-flex align-items-center">
                <h5 class="mb-0"> <i class="bi bi-grid-fill"></i>  ضبط الإعدادات  </h5>
            </div>

            <div class="row g-3 mt-4">
                <div class="col-12">
                    <div class="card shadow-none bg-light border">
                        <div class="card-body">
                            <form class="row align-items-end g-3" id="validateForm" method="post" enctype="multipart/form-data"
                                  action="{{route('admin.settings.store')}}">
                                @csrf
                                    @include('admin_dashboard.settings._form-settings')
                                @include('admin_dashboard.inputs.edit_btn')
                            </form>
                        </div>
                    </div>
                </div>
            </div><!--end row-->
        </div>
    </div>

@endsection
