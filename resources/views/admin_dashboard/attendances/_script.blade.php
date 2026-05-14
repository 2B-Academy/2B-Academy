<script src="https://unpkg.com/qr-code-styling/lib/qr-code-styling.js"></script>
<script src="https://printjs-4de6.kxcdn.com/print.min.js"></script>
<script>

    $(document).ready(function (){
        let fullUrl = "{{$url}}";
        generateQRCodeWithLogo(fullUrl);
    });
    //The doc from : [ https://github.com/kozakdenys/qr-code-styling ] and script in at the top of this page
    function generateQRCodeWithLogo(url, download = false, qrId = 'qrcode-container') {

        const qrCode = new QRCodeStyling({
            width: 400,
            height: 400,
            type: "canvas",
            data: url,
            margin: 0,
            qrOptions: {
                errorCorrectionLevel: "H"
            },
            dotsOptions: {
                type: "rounded",
                color: "#000000",
                gradient: null
            },
            cornersSquareOptions: {
                type: "rounded",
                color: "#000000"
            },
            cornersDotOptions: {
                type: "rounded",
                color: "#000000"
            },
            backgroundOptions: {
                color: "#ffffff"
            },
            image: "{{ asset('front/assets/images/logo/orange_dark.png') }}",
            imageOptions: {
                margin: 0,
                imageSize: 0.22,
                hideBackgroundDots: true,
                crossOrigin: "anonymous"
            }
        });

        const container = document.getElementById(qrId);
        if (!download) {
            container.innerHTML = "";
            qrCode.append(container);
        }
        if (download) {
            qrCode.download({
                name: "attendance-qr-code",
                extension: "png"
            });
        }
    }

    //download_qr
    $(document).on('click', '.download_qr', function() {
        const url = $(this).data('url');
        const id = $(this).attr('id');
        generateQRCodeWithLogo(url, true, id);
    });

</script>

