@extends('front.layouts.master')

@section('pageTitle') اختبار عام | {{$form->title}} @endsection

@push('css')
    <style>
        .header , footer{ display: none }
    </style>
@endpush

@section('content')


    <!-- ==================== Breadcrumb Start Here ==================== -->
    <section class="breadcrumb py-20 inner-banner position-relative z-1 overflow-hidden mb-0">
        <div class="overlay-banner"></div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="breadcrumb__wrapper">
                        <h2 class="breadcrumb__title mt-10 fw-semibold text-white text-center"> {{$form->title}}</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ==================== Breadcrumb End Here ==================== -->



    <div class="container">
        <div class="row">
            <div class="col-12">
                @include('errors.validation_error_front')
            </div>
        </div>
    </div>

    <!-- ======================  Form Section Start ========================= -->
    <section class="contact-form-section py-50 position-relative z-1">
        <div class="container">
            <div class="row my-10">
                <div class="col-12">
                    <div class="labels d-lg-flex align-items-center justify-content-center gap-50">
                        <div class="bg-success text-white p-10 rounded text-center my-2">
                            <small> مدة الإختبار : {{$form->duration}} دقيقة </small>
                        </div>
                        <div class="bg-success text-white p-10 rounded text-center my-2">
                            <small> درجة الإختبار : {{$form->full_mark}} درجة </small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row gy-5 align-items-center">
                <div class="col-md-6 mx-auto mt-50">
                    <div class="p-24 pt-0 rounded-12 box-shadow-md">
                        <div class="border border-neutral-30 rounded-8 bg-main-25 p-24">
                            <form action="{{route('front.forms.user.store', $form->uuid)}}" method="POST" id="userForm">
                                @csrf
                                <div class="d-flex flex-column align-items-center justify-content-around">
                                    <div class="mb-24 w-100 mx-10">
                                        <label for="name" class="text-neutral-700 text-lg fw-medium mb-12">الأسم </label>
                                        <input type="text" id="name" class="common-input rounded-pill border-transparent focus-border-main-600" required name="name" placeholder="ادخل الأسم...">
                                    </div>
                                    <div class="mb-24 w-100 mx-10">
                                        <label for="machine_code" class="text-neutral-700 text-lg fw-medium mb-12">كود الموظف </label>
                                        <input type="text" id="machine_code" class="common-input rounded-pill border-transparent focus-border-main-600" required name="machine_code" placeholder="ادخل كود الموظف...">
                                    </div>
                                    <div class="mb-0">
                                        <button type="submit" id="startBtn" class="btn btn-main rounded-pill flex-center gap-8 mt-10">
                                            ابدأ الإختبار
                                            <i class="ph-bold ph-arrow-up-right d-flex text-lg"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ======================  Form Section End ========================= -->




@endsection

@push('js')
    <script>
        $(document).on('submit', '#userForm', function (e){
            e.preventDefault();
            var url = $(this).attr('action');
            var data = $(this).serialize();
            $.ajax({
                url: url,
                type: 'POST',
                data: data,
                beforeSend:function(){
                    $('#startBtn').attr('disabled', true);
                    $('#startBtn').html('جاري ... <i class="ph-bold ph-arrow-up-right d-flex text-lg"></i>');
                },
                success: function(response) {
                    if(response.status)
                    {
                        swal({
                            title: "نجح",
                            text:  'ابدأ الإختبار الآن',
                            icon: "success",
                            button: {
                                text: "خروج",
                                value: true,
                                visible: true,
                                closeModal: true
                            }
                        });
                        location.href = response.data.redirect_url;
                    }
                    else
                    {
                        swal({
                            title: "عفواً",
                            text:  response.message,
                            icon: "error",
                            button: {
                                text: "خروج",
                                value: true,
                                visible: true,
                                closeModal: true
                            }
                        })
                    }
                    $('#startBtn').attr('disabled', false);
                    $('#startBtn').html('ابدأ الإختبار <i class="ph-bold ph-arrow-up-right d-flex text-lg"></i>');
                },
                error: function (response) {
                    let errors = response.responseJSON.errors;
                    if (errors) {
                        var all_errors = '';
                        $.each(errors, function (field, messages) {
                            $.each(messages, function (index, message) {
                                all_errors += message;
                            });
                        });
                    }
                    swal({
                        title: "عفواً",
                        text:  all_errors,
                        icon: "error",
                        button: {
                            text: "خروج",
                            value: true,
                            visible: true,
                            closeModal: true
                        }
                    })
                    $('#startBtn').attr('disabled', false);
                    $('#startBtn').html('ابدأ الإختبار <i class="ph-bold ph-arrow-up-right d-flex text-lg"></i>');
                }
            })
        });
    </script>
@endpush
