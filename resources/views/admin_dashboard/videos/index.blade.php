@extends('admin_dashboard.layout.master')
@section('Page_Title')  الفيديوهات @endsection

@section('content')


    <div class="card">
        <div class="card-body">

            <div class="d-flex align-items-center">
                <h5 class="mb-0"> <i class="bi bi-grid-fill"></i>  الفيديوهات </h5>
                <div class="ms-auto position-relative">
                    <a href="{{route('admin.videos.create')}}" class="btnIcon btn btn-outline-primary px-5"><i class="lni lni-circle-plus"></i> إنشاء </a>
                </div>
            </div>

            <div class="mt-5 mb-2 row">
                <form method="GET" action="" class="col-12">
                    <div class="row m-0">
                        <div class="col-md-8 mb-2">
                            <input type="text" class="form-control" value="{{ request('search') }}" name="search" placeholder="ابحث عن الفيديو...">
                        </div>
                        <div class="col-md-4 mb-2">
                            <button type="submit" class="btn btn-success">بحث</button>
                            <button type="reset" class="btn btn-dark" onclick="location.href='{{route('admin.videos.index')}}'">إزالة البحث</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="table-responsive mt-4">
                <table class="table align-middle table-hover">
                    <thead class="table-secondary">
                    <tr>
                        <Th>الأسم</Th>
                        <Th>الفيديو</Th>
                        <Th>النسخ</Th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($content as $con)
                        <tr>
                            <td>{{$con['name']}}</td>
                            <td>
                                <a target="_blank" href="{{$con['url']}}">{{$con['url']}}</a>
                            </td>
                            <td>
                                <button type="button" onclick="copyText('{{$con['url']}}')" class="btn btn-main">نسخ رابط الفيديو</button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div>

                </div>
            </div>
        </div>
    </div>


@endsection

@push('js')
    @include('admin_dashboard.components.delete')

    <script>
        function copyText(element) {
            navigator.clipboard.writeText(element)
                .then(() => {
                    swal({
                        title: "تم النسخ بنجاح",
                        text:  element,
                        icon: "success"
                    });
                })
                .catch(err => {
                    console.error('Failed to copy: ', err);
                });
        }
    </script>
@endpush
