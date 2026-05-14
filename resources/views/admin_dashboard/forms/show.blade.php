@extends('admin_dashboard.layout.master')
@section('Page_Title')   الأختبارات العامة | الدرجات   @endsection

@section('content')

    <div class="row">
        <div class="col-lg-12 mx-auto">
            <div class="breadcrumb d-flex align-items-center justify-content-between">
                <div class="">
                    <a class="text-dark" href="{{route('admin.forms.index')}}">الأختبارات العامة</a>
                    <span class="mx-2">-</span>
                    <strong class="text-primary">الدرجات</strong>
                    <span class="mx-2">-</span>
                    <strong class="text-primary">{{$content->title}}</strong>
                </div>
            </div>
            <div class="card">
                <div class="card-body">

                    <div class="row g-3 mt-4">
                        <div class="col-12 mb-3">
                            <a href="{{route('admin.forms.export', $content)}}" class="btn btn-success">
                                <i class="lni lni-download mx-1"></i> تصدير تقرير الدرجات
                            </a>
                            <a href="{{route('admin.forms.export.questions', $content)}}" class="btn btn-dark mx-4">
                                <i class="lni lni-download mx-1"></i> تصدير الأسئلة
                            </a>
                            <a href="{{route('admin.forms.export.questions.text', $content)}}" class="btn btn-warning">
                                <i class="lni lni-download mx-1"></i> تصدير الأسئلة الكتابية
                            </a>

                            <a href="{{route('admin.forms.export.questions.wrong', $content)}}" class="btn btn-danger">
                                <i class="lni lni-download mx-1"></i> تصدير الأسئلة الخاطئة للموظفين
                            </a>
                        </div>
                        <div class="col-12">
                            <div class="card shadow-none bg-light border">
                                <table class="table align-middle table-hover">
                                    <thead class="table-secondary">
                                    <tr>
                                        <th>الكود</th>
                                        <Th>الأسم</Th>
                                        <Th>الدرجة</Th>
                                        <Th>المدة المستغرقة</Th>
                                        <th>تاريخ البدء</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($content->users as $con)
                                        <tr>
                                            <td>{{$con->machine_code}}</td>
                                            <td>{{$con->name}}</td>
                                            <td>
                                                <div class="circle text-center" style="margin: 0 50px;background: #f37021;border-radius: 50px;color: #fff9f5;">
                                                    <strong>{{$con->mark}}</strong>
                                                    <hr style="margin: 2px 0 !important;opacity: 1">
                                                    <strong>{{$content->full_mark}}</strong>
                                                </div>
                                            </td>
                                            <td>{{$con->duration}} دقيقة</td>
                                            <td>{{date('Y-m-d H:i A', strtotime($con->start_at))}}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">
                                                <p>لا يوجد بيانات</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div><!--end row-->
                </div>
            </div>
        </div>
    </div>

@endsection
