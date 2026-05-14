@extends('admin_dashboard.layout.master')
@section('Page_Title')  الشهادات @endsection

@section('content')


    <div class="card">
        <div class="card-body">

            <div class="d-flex align-items-center">
                <h5 class="mb-0"> <i class="bi bi-grid-fill"></i>  الشهادات </h5>
            </div>

            <div class="mt-5 mb-2 row">
                <div class="col-md-12 text-center">
                    <div class="certificate-card">
                        <div class="user-certificate" id="certificate">
                            <img src="data:image/jpeg;base64,{{ $user_certificate }}" alt="Certificate" class="img-fluid rounded shadow">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

@push('js')
@endpush
