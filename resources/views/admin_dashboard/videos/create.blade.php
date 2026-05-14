@extends('admin_dashboard.layout.master')
@section('Page_Title')   الفيديوهات | إنشاء   @endsection
@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css" />
    <style>
        .dz-message {
            text-align: center;
            font-size: 1.2rem;
            margin: 2rem 0;
        }
        .progress {
            height: 25px;
        }
        .progress-bar {
            font-weight: bold;
        }
        .dropzone
        {
            min-height: 150px;
            background: #fff;
            border: none;
            padding: 20px 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            border-radius: 20px;
            box-shadow: 0 0 10px #bbbbbb;
        }
        .icon i
        {
            font-size: 50px !important;
            color: #c0c0c0;
        }
    </style>
@endpush
@section('content')

    <div class="row">
        <div class="col-lg-12 mx-auto">
            <div class="breadcrumb d-flex align-items-center justify-content-between">
                <div class="">
                    <a class="text-dark" href="{{route('admin.videos.index')}}">الفيديوهات</a>
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

                                    <form action="{{ route('admin.videos.store') }}" class="dropzone" id="videoDropzone" enctype="multipart/form-data">
                                        @csrf
                                        <div class="icon">
                                            <i class="bx bx-upload"></i>
                                        </div>
                                        <div class="dz-message">اسحب الفيديو هنا أو اضغط للاختيار</div>
                                        <small class="text-danger">الصيغ المسموح بها ( mp4 - mpeg - ogg - webm )</small>
                                    </form>
                                    <div class="progress mt-4" style="display:none;">
                                        <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%">0%</div>
                                    </div>


                                    <div id="status" class="mt-3"></div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>

    <script>
        Dropzone.autoDiscover = false;

        const dropzone = new Dropzone("#videoDropzone", {
            chunking: true,
            forceChunking: true,
            url: "{{ route('admin.videos.store') }}",
            maxFilesize: 2048, // MB
            chunkSize: 10 * 1024 * 1024, // 2 MB
            parallelChunkUploads: true,
            retryChunks: true,
            retryChunksLimit: 3,
            addRemoveLinks: true,
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            acceptedFiles: 'video/*',
            init: function() {
                const progressWrapper = document.querySelector(".progress");
                const progressBar = document.getElementById("progressBar");
                const statusDiv = document.getElementById("status");

                this.on("uploadprogress", function(file, progress) {
                    progressWrapper.style.display = "block";
                    progressBar.style.width = progress + "%";
                    progressBar.textContent = Math.round(progress) + "%";
                });

                this.on("success", function(file, response) {
                    progressBar.style.width = "100%";
                    progressBar.textContent = "100%";
                    statusDiv.innerHTML = `<div class="alert alert-success">${response.message}<br>مسار الفيديو:<br> <div id="videoPath">${response.path}</div> <br><button id="copyBtn" class="btn btn-sm btn-primary">نسخ مسار الفيديو</button></div>`;
                });

                this.on("error", function(file, response) {
                    statusDiv.innerHTML = `<div class="alert alert-danger">حدث خطأ أثناء رفع الفيديو</div>`;
                });

                this.on("removedfile", function(file) {
                    progressWrapper.style.display = "none";
                    progressBar.style.width = "0%";
                    progressBar.textContent = "0%";
                    statusDiv.innerHTML = "";
                });
            }
        });

        // زر النسخ باستخدام jQuery
        $(document).on('click','#copyBtn',function() {
            const text = document.getElementById('videoPath').innerText;
            navigator.clipboard.writeText(text).then(() => {
                    swal({
                        title: "نسخ الرابط",
                        text: "تم النسخ بنجاح.",
                        icon: "success"
                    })
                })
                .catch(err => {
                    console.error('Failed to copy: ', err);
                });
        });
    </script>
@endpush
