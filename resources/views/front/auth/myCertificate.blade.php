@extends('front.layouts.master')

@section('pageTitle') شهاداتي @endsection


@section('content')


    @include('front.includes.inner-head', ['title' => 'شهاداتي'])


    <!-- =========== student dashbord section start ============== -->
    <section class="bg-main-25 py-60 w-100 h-100">
        <div class="container container--lg">
            <div class="d-flex gap-24  z-2 position-relative">
                @include('front.auth.includes.sidebar')
                <div class="w-100">
                    <div class="mb-32">
                        <div class="d-flex align-items-center gap-16 justify-content-between">
                            <h5 class="mb-16">شهاداتي</h5>
                            <button type="button" class="toggle-student-dashbord-button  text-32 d-xl-none d-block">
                                <i class="ph-bold ph-list"></i>
                            </button>
                        </div>
                        <div class="my-24">

                            <div class="overflow-x-auto">
                                <div class="row mx-0">
                                    <div class="col-md-6">
                                        <form method="GET" action="" class="mb-4">
                                            <input class="form-control" name="name" value="{{request('name') ?? auth()->user()->name}}" required>
                                            <button type="submit" class="btn  btn-sm my-3 btn-primary">تغيير الأسم علي الشهادة</button>
                                        </form>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-30 d-flex align-items-center justify-content-center gap-14">
                                            <a class="btn btn-dark" onclick="downloadImage()">
                                                <i class="ph-bold ph-download mx-1"></i>
                                                تحميل الشهادة كصورة
                                            </a>
                                            <a class="btn btn-dark" onclick="downloadPdf()">
                                                <i class="ph-bold ph-download mx-1"></i>
                                                تحميل الشهادة PDF
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-md-12 text-center">
                                        <div class="certificate-card">
                                            <div class="user-certificate" id="certificate">
                                                <img src="data:image/jpeg;base64,{{ $user_certificate }}" alt="Certificate" class="img-fluid rounded shadow">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- =========== student dashbord section end ============== -->



@endsection

@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        /**
         * Generating PDF from HTML using jQuery
         */
        // Download as image
        function downloadImage() {
            html2canvas(document.querySelector("#certificate"), {scale:2}).then(canvas => {
                let link = document.createElement("a");
                link.download = "certificate.png";
                link.href = canvas.toDataURL("image/png");
                link.click();
            });
        }

        // Download as PDF
        function downloadPdf() {
            html2canvas(document.querySelector("#certificate"), {scale:1}).then(canvas => {
                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF("l", "pt", [canvas.width, canvas.height]);
                const imgData = canvas.toDataURL("image/png");
                pdf.addImage(imgData, "PNG", 0, 0, canvas.width, canvas.height);
                pdf.save("certificate.pdf");
            });
        }
    </script>
@endpush
