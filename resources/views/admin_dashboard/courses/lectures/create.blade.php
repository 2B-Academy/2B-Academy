@extends('admin_dashboard.layout.master')
@section('Page_Title')   المحاضرات | إنشاء   @endsection

@section('content')

    <div class="row">
        <div class="col-lg-12 mx-auto">
            <div class="breadcrumb d-flex align-items-center justify-content-between">
                <div class="">
                    <a class="text-dark" href="{{route('admin.courses.edit', $course)}}">{{$course->title}}</a>
                    <span class="mx-2">-</span>
                    <a class="text-dark" href="{{route('admin.courses.lectures.index', $course)}}">المحاضرات</a>
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
                                          action="{{route('admin.courses.lectures.store', $course)}}">
                                        @csrf
                                        @include('admin_dashboard.courses.lectures._form')
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
        document.getElementById('type').addEventListener('change', function() {
            if (this.value === 'url') {
                document.getElementById('urlDiv').style.display = 'none';
            } else {
                document.getElementById('urlDiv').style.display = 'block';
            }
        });

        document.getElementById('chooseFileBtn').addEventListener('click', function() {
            fetch('/admin/videos/list')
                .then(res => res.json())
                .then(files => {
                    let html = '<ul class="list-group">';
                    files.forEach(file => {
                        html += `<li class="list-group-item">
                            <a href="#" class="choose-file" data-url="${file.url}">
                                ${file.name}
                            </a>
                         </li>`;
                    });
                    html += '</ul>';
                    document.getElementById('fileList').innerHTML = html;

                    // Show modal
                    var modal = new bootstrap.Modal(document.getElementById('fileModal'));
                    modal.show();

                    // Handle click
                    document.querySelectorAll('.choose-file').forEach(el => {
                        el.addEventListener('click', function(e) {
                            e.preventDefault();
                            document.getElementById('videoUpload').value = this.dataset.url;
                            modal.hide();
                        });
                    });
                });
        });
    </script>
@endpush
