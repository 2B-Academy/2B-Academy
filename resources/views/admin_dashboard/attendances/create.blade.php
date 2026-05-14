@extends('admin_dashboard.layout.master')
@section('Page_Title')   الحضور | إنشاء   @endsection
@push('css')
    <style>
        .userInfoHeader
        {
            background: var(--bs-orange);
            padding: 13px;
            border-radius: 50px;
            color: #fff;
        }
        .userInfoHeader h6
        {
            margin-bottom: 0;
        }
        .attendanceInfoHeader h6
        {
            background: #ff7400;
            border-radius: 50px;
            padding: 5px 20px;
            font-size: 14px;
            color: #fff;
        }
    </style>
@endpush
@section('content')

    <div class="row">
        <div class="col-lg-12 mx-auto">
            <div class="breadcrumb d-flex align-items-center justify-content-between">
                <div class="">
                    <a class="text-dark" href="{{route('admin.attendances.index')}}">الحضور</a>
                    <span class="mx-2">-</span>
                    <strong class="text-primary">إنشاء</strong>
                </div>
            </div>
            <div class="card">
                <div class="card-body">

                    <form method="GET" action="" class="row g-3 mt-4 align-items-end">

                        <div class="col-md-3 mb-2">
                            <label for="course">الدورة التدريبية: <small class="text-danger">*</small></label>
                            <select name="course_id" id="course_id" class="form-control select2" required>
                                <option value="">كل الدورات التدريبية</option>
                                @foreach($allCourses as $id => $title)
                                    <option value="{{ $id }}" {{ $selectedCourse == $id ? 'selected' : '' }}>
                                        {{ $title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label for="group_id">المجموعة: <small class="text-danger">*</small></label>
                            <select name="group_id" id="group_id" required class="form-control select2">
                                @if(!is_null(request('course_id')) && !is_null(request('group_id')))
                                    @php
                                        $groups = \App\Models\Course::with('sections')->whereId(request('course_id'))->first();
                                    @endphp
                                    @foreach($groups->sections as $group)
                                        <option value="{{$group->id}}" @selected($group->id == request('group_id'))>
                                            {{$group->name}}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label for="user">الموظف:</label>
                            <select name="user_id"  id="allUsers" class="form-control">
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <button type="submit" class="btn btn-main">فلتر</button>
                            <button type="reset" onclick="location.href='{{route('admin.attendances.create')}}'" class="mx-2 btn btn-danger">إلغاء</button>
                        </div>
                    </form>

                    <div class="row g-3 mt-5">
                        <div class="col-12">
                            <div class="card shadow-none bg-light border">
                                <div class="card-body">
                                    @if($show_form)
                                        @include('admin_dashboard.attendances._form')
                                    @endif
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>

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


            $(document).on('change', '.attendance-toggle', function (){
                var course_id = $(this).data('course');
                var user_id   = $(this).data('user');
                var checked   = $(this).is(':checked');
                $.ajax({
                    url: "{{route('admin.attendances.store')}}",
                    type: 'POST',
                    data: {
                        _token: '{{csrf_token()}}',
                        course_id: course_id,
                        user_id: user_id,
                        status: checked ? 1 : 0
                    },
                    success: function(response) {
                        console.log(response);
                    }
                })
            });


            //
            $(document).on('click', '.compare-attendance-dates', function (){
                var _self = $(this);
                _self.attr('disabled', true);
                _self.html('جاري...');
                var user_id    = _self.data('user');
                var section_id = _self.data('section');
                $.ajax({
                    url: "{{route('admin.attendances.compare-attendance-dates')}}",
                    type: 'GET',
                    data: {
                        'user_id' : user_id,
                        'section_id' : section_id
                    },
                    success: function(response) {
                        if(response.status)
                        {
                            var res = response.data;
                            $('#codeName').html(res.user.machine_code);
                            $('#userName').html(res.user.name);
                            $('#attendance_count').html(res.attendances_count);
                            $('#sessions_count').html(res.sessions_count);
                            $('#user-appointments').html(res.attendances_dates_html);
                            $('#sessions-appointments').html(res.sessions_html);
                        }
                        else
                        {
                            $('.modal-body').html('Something Error , Refresh page and try again');
                        }
                        var modal = new bootstrap.Modal(document.getElementById('appointmentTable'));
                        modal.show();
                        _self.attr('disabled', false);
                        _self.html('<i class="bx bx-git-compare mx-1"></i> مقارنة المواعيد');
                    }
                })
            });



        </script>
    @endpush
@endpush
