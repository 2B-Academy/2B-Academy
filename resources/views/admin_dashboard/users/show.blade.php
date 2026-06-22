@extends('admin_dashboard.layout.master')
@section('Page_Title')   الموظفين | مشاهدة   @endsection
@push('css')
    <style>
        .nav-link.active
        {
            background-color: #f37021 !important;
            color: #fff !important;
        }
    </style>
@endpush
@section('content')

    <div class="row">
        <div class="col-lg-12 mx-auto">
            <div class="breadcrumb d-flex align-items-center justify-content-between">
                <div class="">
                    <a class="text-dark" href="{{route('admin.users.index')}}">الموظفين</a>
                    <span class="mx-2">-</span>
                    <strong class="text-primary">مشاهدة</strong>
                    <span class="mx-2">-</span>
                    <strong class="text-primary">{{$content->name}}</strong>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="row my-3 mx-0">
                        <div class="col-md-6">
                            <h6 class="mb-2"> كود الموظف : {{$content->machine_code}}</h6>
                            <h6 class="mb-2"> الأسم : {{$content->name}}</h6>
                            <h6 class="mb-2"> المسمى الوظيفي : {{$content->job_title}}</h6>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-2"> البريد الإلكتروني : {{$content->email}}</h6>
                            <h6 class="mb-2"> القسم : {{$content->department_name}}</h6>
                        </div>
                    </div>

                    <div class="row g-3 mt-4">
                        <div class="col-12">
                            <div class="card shadow-none bg-light border">
                                <div class="card-body">
                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="courses-tab" data-bs-toggle="tab" data-bs-target="#courses"
                                                    type="button" role="tab" aria-controls="courses" aria-selected="true">الدورات التدريبية</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="ratings-tab" data-bs-toggle="tab" data-bs-target="#ratings"
                                                    type="button" role="tab" aria-controls="ratings" aria-selected="false">التقييمات</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="lectureQuestions-tab" data-bs-toggle="tab" data-bs-target="#lectureQuestions"
                                                    type="button" role="tab" aria-controls="lectureQuestions" aria-selected="false">أسئلة المحاضرات</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="exams-tab" data-bs-toggle="tab" data-bs-target="#exams"
                                                    type="button" role="tab" aria-controls="exams" aria-selected="false">الأختبارات</button>
                                        </li>
                                    </ul>
                                    <div class="tab-content" id="myTabContent">
                                        <div class="tab-pane fade show active" id="courses" role="tabpanel" aria-labelledby="courses">
                                            @include('admin_dashboard.users.includes.courses')
                                        </div>
                                        <div class="tab-pane fade" id="ratings" role="tabpanel" aria-labelledby="ratings">
                                            @include('admin_dashboard.users.includes.ratings')
                                        </div>
                                        <div class="tab-pane fade" id="lectureQuestions" role="tabpanel" aria-labelledby="lectureQuestions">
                                            @include('admin_dashboard.users.includes.lectureQuestions')
                                        </div>
                                        <div class="tab-pane fade" id="exams" role="tabpanel" aria-labelledby="exams">
                                            @include('admin_dashboard.users.includes.exams')
                                        </div>
                                    </div>
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
    @include('admin_dashboard.components.delete')
    <script>
        $(document).on('submit', '#addAnswer', function (e){
            e.preventDefault();
            var url = $(this).attr('action');
            var data = $(this).serialize();
            $.ajax({
                url: url,
                type: 'POST',
                data: data,
                success: function(response) {
                    if(response.status)
                    {
                        swal({
                            title: response.message,
                            text: "شكراً لك.",
                            icon: "success",
                            button: {
                                text: "خروج",
                                value: true,
                                visible: true,
                                closeModal: true
                            }
                        })
                    }
                }
            })
        });
    </script>
@endpush
