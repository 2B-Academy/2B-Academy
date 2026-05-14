<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الحضور</title>
    <link rel="shortcut icon" href="{{asset('front/assets/images/logo/2b_logo.svg')}}">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;800&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Cairo', sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f0f0f, #1a1a1a);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Glass Card */
        .glass-card {
            width: 420px;
            padding: 35px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 20px 40px rgba(0,0,0,0.6);
            animation: fadeUp 1s ease;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Logo */
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo img {
            width: 90px;
        }
        .logo h3 {
            color: #fff;
            font-weight: 800;
        }
        .logo span {
            color: #ff8c00;
        }

        /* Inputs */
        .form-control,
        .form-select {
            background-color: transparent !important;
            color: #fff;
            border: 1px solid rgba(255,255,255,0.2);
            padding: 12px;
            border-radius: 12px;
        }

        .form-control::placeholder {
            color: rgba(255,255,255,0.6);
        }
        .alert-danger
        {
            font-weight: bold;
            font-size: 12px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #ff8c00;
            box-shadow: 0 0 0 0.15rem rgba(255,140,0,0.25);
            background-color: transparent !important;
            color: #fff;
        }
        /* Select dropdown fix */
        .form-select option {
            background-color: #1a1a1a;
            color: #fff;
        }
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus,
        select:-webkit-autofill {
            -webkit-box-shadow: 0 0 0 1000px transparent inset !important;
            -webkit-text-fill-color: #fff !important;
            transition: background-color 5000s ease-in-out 0s;
        }

        /* Button */
        .btn-orange {
            background: linear-gradient(135deg, #ff8c00, #ff6a00);
            border: none;
            padding: 12px;
            border-radius: 14px;
            font-weight: 700;
            transition: 0.3s;
        }

        .btn-orange:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(255,140,0,0.4);
        }

        /* Message */
        #message {
            margin-top: 15px;
        }
        .success-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 28px;
            animation: scaleIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 12px 32px rgba(16, 185, 129, 0.3);
            position: relative;
        }

        .success-icon::before {
            content: '';
            position: absolute;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: rgba(16, 185, 129, 0.1);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                opacity: 0.5;
            }
            50% {
                transform: scale(1.15);
                opacity: 0;
            }
        }

        @keyframes scaleIn {
            0% {
                transform: scale(0) rotate(-180deg);
                opacity: 0;
            }
            100% {
                transform: scale(1) rotate(0);
                opacity: 1;
            }
        }

        .success-icon::after {
            content: '✓';
            font-size: 56px;
            color: white;
            font-weight: bold;
            position: relative;
            z-index: 1;
        }

        .success-message h3 {
            font-size: 32px;
            margin-bottom: 14px;
        }

        .success-message p {
            font-size: 17px;
            color: #64748b;
            line-height: 1.7;
        }

    </style>
</head>

<body>

<div class="glass-card">

    <!-- Logo -->
    <div class="logo d-flex align-items-center justify-content-between">
        <img src="{{ asset('front/assets/images/logo/orange_white.png') }}" alt="Logo">
        <small class="text-light opacity-75">تسجيل الحضور</small>
    </div>


    <div class="form-data" id="form-data">
        <!-- Form -->
        <form id="attendanceForm" action="{{route('front.attendances.store')}}" method="POST">
            @csrf
            <div class="mb-3">
                <input type="text" id="user_machine_code" name="user_machine_code" required class="form-control" placeholder="ادخل الكود الوظيفي">
            </div>

            <div class="mb-3 d-none" id="other_inputs">
                <div class="mb-3">
                    <select id="course_id" name="course_id" class="form-control form-select" required></select>
                </div>
            </div>

            <button type="button" class="btn btn-orange text-white w-100" id="complete-after-code">
                استكمال <i class="fa fa-arrow-left mx-2"></i>
            </button>
            <button type="submit" class="btn btn-orange text-white w-100 d-none" id="submitFormButton">
                سجل حضور الآن <i class="fa fa-arrow-left mx-2"></i>
            </button>

        </form>

        <div id="message" class="text-center"></div>
    </div>

    <div class="thanks d-none" id="thanks">
        <div class="success-message text-white text-center">
            <div class="success-icon"></div>
            <h3>شكراً لك!</h3>
            <p>تم تسجيل الحضور بنجاح</p>
        </div>
    </div>

</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).on('click', '#complete-after-code', function (e) {
        e.preventDefault();
        let user_machine_code = $('#user_machine_code').val().trim();
        if (!user_machine_code) {
            $('#message').html('<div class="alert alert-danger mt-3">من فضلك ادخل الكود الوظيفي</div>');
            return;
        }
        $.ajax({
            url: "{{route('front.attendances.getUser')}}?user_machine_code="+user_machine_code,
            type: 'GET',
            beforeSend: function (){
                $('#complete-after-code').attr('disabled', true);
            },
            success: function(response) {
                if(response.status)
                {
                    $('#message').html('');
                    $('#other_inputs').removeClass('d-none');
                    $('#course_id').html(response.data.html);
                    $('#complete-after-code').addClass('d-none');
                    $('#submitFormButton').removeClass('d-none');
                    $('#user_machine_code').attr('readonly', true)
                }
            },
            error: function (response) {
                $('#message').html('<div class="alert alert-danger mt-3">الكود الوظيفي غير صحيح</div>');
                $('#complete-after-code').attr('disabled', false);
            }
        });
    });


    $(document).on('click', '#submitFormButton', function (e) {
        e.preventDefault();
        $(this).attr('disabled', true);
        let user_machine_code = $('#user_machine_code').val().trim();
        let course_id = $('#course_id').val().trim();
        if (!user_machine_code || !course_id) {
            $('#message').html('<div class="alert alert-danger mt-3">من فضلك الحقول مطلوبة</div>');
            return;
        }
        var form = $('#attendanceForm');
        let formData = new FormData(form[0]);
        var url = form.attr('action');
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if(response.status)
                {
                    $('#form-data').remove();
                    $('#thanks').removeClass('d-none');
                    $('#thanks').fadeIn(500);
                }
                else
                {
                    $('#message').html('<div class="alert alert-danger mt-3">'+response.message+'</div>');
                    $('#submitFormButton').attr('disabled', false);
                }
            },
            error: function (response) {
                $('#message').html('<div class="alert alert-danger mt-3">'+response.responseJSON.message+'</div>');
                $('#submitFormButton').attr('disabled', false);
            }
        });
    });

</script>

</body>
</html>
