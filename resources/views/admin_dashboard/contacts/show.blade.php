@extends('admin_dashboard.layout.master')
@section('Page_Title')   فورم تواصل معنا | مشاهدة   @endsection

@section('content')

    <div class="row">
        <div class="col-lg-12 mx-auto">
            <div class="breadcrumb d-flex align-items-center justify-content-between">
                <div class="">
                    <a class="text-dark" href="{{route('admin.contacts.index')}}">فورم تواصل معنا</a>
                    <span class="mx-2">-</span>
                    <strong class="text-primary">مشاهدة</strong>
                    <span class="mx-2">-</span>
                    <strong class="text-primary">{{$content->name}}</strong>
                </div>
            </div>
            <div class="card">
                <div class="card-body">

                    <div class="row g-3 mt-4">
                        <div class="col-12">
                            <div class="card shadow-none bg-light border">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="my-3">
                                                <h6>الأسم :</h6>
                                                <h5> <strong>{{$content->name}}</strong></h5>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="my-3">
                                                <h6>البريد الإلكتروني :</h6>
                                                <h5> <strong>{{$content->email}}</strong></h5>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="my-3">
                                                <h6>رقم الهاتف : </h6>
                                                <h5><strong>{{$content->mobile}}</strong></h5>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="my-3">
                                                <h6>أنشي في :</h6>
                                                <h5><strong>{{date('Y-m-d H:i A', strtotime($content->created_at))}}</strong></h5>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="my-3">
                                                <h6>الرسالة :</h6>
                                                <h5> <strong>{{$content->message}}</strong></h5>
                                            </div>
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
