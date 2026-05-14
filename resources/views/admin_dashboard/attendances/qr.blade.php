@extends('admin_dashboard.layout.master')
@section('Page_Title')  Qr Code الحضور @endsection
@push('css')
    <link rel="stylesheet" href="https://printjs-4de6.kxcdn.com/print.min.css">

    <style>
        @media print {

            @page {
                size: auto;
                margin: 0;
            }

            body {
                margin: 0;
            }

            .print-area {
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: auto;
                height: auto;
                text-align: center;
            }

            #qrcode-container {
                padding: 20px;
                border: 1px solid #ededed;
                border-radius: 15px;
            }

            canvas {
                display: block;
                margin: auto;
            }
        }
    </style>
@endpush
@section('content')


    <div class="card">
        <div class="card-body">

            <div class="d-flex align-items-center">
                <h5 class="mb-0"> <i class="bi bi-grid-fill"></i>  Qr Code الحضور </h5>
            </div>
            <div class="qr-code">
                <div class="text-center print-area" id="print">
                    <div id="qrcode-container" class="text-center"
                         style="position: relative;padding: 20px;margin: 30px 0 0;border: 1px solid #ededed;border-radius: 15px;"></div>
                </div>
                <div class="text-center">
                    <button type="button" class="btn btn-main download_qr p-2 mt-4" data-url="{{$attendance_url}}"><i class="lni lni-download mx-1"></i> تنزيل</button>
                    <button type="button" class="btn btn-dark p-2 mt-4" onclick="printJS('print', 'html')">
                        <i class="bx bx-printer mx-1"></i> طباعة
                    </button>
                </div>
            </div>
        </div>
    </div>


@endsection

@push('js')
    @include('admin_dashboard.attendances._script', ['url' => $attendance_url])
@endpush
