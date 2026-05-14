@extends('admin_dashboard.layout.master')
@section('Page_Title')   مستخدمين الدورات الأوفلاين | إنشاء   @endsection

@section('content')

    <div class="row">
        <div class="col-lg-12 mx-auto">
            <div class="breadcrumb d-flex align-items-center justify-content-between">
                <div class="">
                    <a class="text-dark" href="{{route('admin.users-courses-offline.index')}}">مستخدمين الدورات الأوفلاين</a>
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
                                          action="{{route('admin.users-courses-offline.store')}}">
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
        $(document).on('change', '#course_id', function (){
            var course_id = $(this).val();
            $.ajax({
                url: "{{route('admin.get-groups-of-course')}}",
                type: 'GET',
                data: {
                    'course_id' : course_id
                },
                success: function(response) {
                    if(response.status)
                    {
                       $('#group_id').html(response.data.html);
                    }
                    else{
                        $('#group_id').html('');
                    }

                }
            })
        });
    </script>
@endpush
