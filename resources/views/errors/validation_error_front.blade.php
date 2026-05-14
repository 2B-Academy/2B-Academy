<!-- Errors -->
@if ($errors->any())
    <div class="div_errors">
        @foreach ($errors->all() as $error)
            <div class="alert-danger alert text-center d-flex justify-content-center align-items-center py-3 fs-14">
                <i class="fa fa-times-circle me-2"></i>
                {{$error}}
            </div>
        @endforeach
    </div>
@endif
<!-- end -->
<!-- Error -->
@if (Session::has('error'))
    <div class="div_errors">
        <div class="alert-danger alert text-center d-flex justify-content-center align-items-center py-3 fs-14">
            <i class="fa fa-times-circle me-2"></i>
            {{Session::get('error')}}
        </div>
    </div>
@endif
<!-- end -->
<!-- Error -->
@if (Session::has('success'))
    <div class="div_errors">
        <div class="alert-success alert text-center d-flex justify-content-center align-items-center py-2 fs-14">
            <i class="fa fa-times-circle me-2"></i>
            {{Session::get('success')}}
        </div>
    </div>
@endif
<!-- end -->
