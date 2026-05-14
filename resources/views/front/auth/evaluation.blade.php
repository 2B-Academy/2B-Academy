@extends('front.layouts.master')

@section('pageTitle') التقييم العام @endsection

@push('css')
    <style>
        .form-card {
            background: white;
            border-radius: 28px;
            padding: 56px;
            box-shadow: 0 20px 60px rgba(102, 126, 234, 0.15);
            position: relative;
            overflow: hidden;
        }
        .form-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.03) 0%, rgba(118, 75, 162, 0.03) 100%);
            pointer-events: none;
        }

        .decorative-element {
            position: absolute;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            opacity: 0.05;
            top: -100px;
            right: -100px;
            z-index: 0;
        }

        .form-content {
            position: relative;
            z-index: 1;
        }

        .form-header {
            text-align: center;
            margin-bottom: 48px;
        }

        .form-title {
            font-size: 36px;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 12px;
            line-height: 1.2;
        }

        .form-subtitle {
            font-size: 17px;
            color: #64748b;
            line-height: 1.6;
            font-weight: 400;
        }

        .progress-container {
            margin-bottom: 48px;
        }

        .progress-bar {
            height: 8px;
            background: #f1f5f9;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 28px;
            position: relative;
        }

        .progress-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg,
            transparent 0%,
            rgba(255, 255, 255, 0.3) 50%,
            transparent 100%);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .progress-steps {
            display: flex;
            justify-content: space-between;
            gap: 16px;
        }

        .progress-step {
            flex: 1;
            text-align: center;
            font-size: 13px;
            font-weight: 600;
            color: #94a3b8;
            transition: all 0.3s ease;
            position: relative;
            padding-top: 32px;
        }

        .progress-step::before {
            content: attr(data-number);
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #f1f5f9;
            color: #94a3b8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            transition: all 0.3s ease;
        }

        .progress-step.active {
            color: #667eea;
        }

        .progress-step.active::before {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .progress-step.completed {
            color: #10b981;
        }

        .progress-step.completed::before {
            content: '✓';
            background: #10b981;
            color: white;
        }

        .form-step {
            display: none;
            animation: fadeSlideIn 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .form-step.active {
            display: block;
        }

        @keyframes fadeSlideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-group {
            margin-bottom: 28px;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 10px;
            letter-spacing: -0.01em;
        }

        .required {
            color: #ef4444;
        }

        input, select, textarea {
            width: 100%;
            padding: 16px 18px;
            font-size: 15px;
            border: 2px solid #e2e8f0;
            border-radius: 14px;
            transition: all 0.3s ease;
            background: #fafbfc;
            color: #1e293b;
        }

        input:hover, select:hover, textarea:hover {
            border-color: #cbd5e1;
            background: white;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        input::placeholder, textarea::placeholder {
            color: #94a3b8;
        }

        textarea {
            resize: vertical;
            min-height: 130px;
            line-height: 1.6;
        }

        .radio-group {
            display: flex;
            align-items: center;
            justify-content: space-evenly;
            background: #efefef;
            padding: 20px 0;
            border-radius: 12px;
        }

        .radio-option {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            background: #ffffff;
            padding: 5px 20px;
            border-radius: 5px;
        }
        .radio-option.ten input[type="radio"]
        {
            width: 30px;
            height: 30px;
        }

        .radio-option input[type="radio"]:checked + .radio-label {
            border-color: #667eea;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%);
            color: #667eea;
            font-weight: 600;
        }

        .radio-option input[type="radio"]:checked + .radio-label::before {
            border-color: #667eea;
            background: #667eea;
            box-shadow: inset 0 0 0 4px white;
        }

        .button-group {
            display: flex;
            gap: 14px;
            margin-top: 40px;
        }

        button {
            flex: 1;
            padding: 18px 36px;
            font-size: 16px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            border: none;
            border-radius: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            letter-spacing: -0.01em;
        }

        .btn-secondary {
            background: #f1f5f9;
            color: #475569;
            border: 2px solid #e2e8f0;
        }

        .btn-secondary:hover {
            background: #e2e8f0;
            transform: translateY(-2px);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.35);
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 32px rgba(102, 126, 234, 0.45);
        }

        .btn-primary:active {
            transform: translateY(-1px);
        }

        .success-message {
            display: none;
            text-align: center;
            animation: fadeSlideIn 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 20px 0;
        }

        .success-message.active {
            display: block;
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

        .error-message {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border: 2px solid #fecaca;
            color: #dc2626;
            padding: 14px 20px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 24px;
            display: none;
            animation: shake 0.4s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-8px); }
            75% { transform: translateX(8px); }
        }

        .error-message.active {
            display: block;
        }


        .rate {
            display: flex;
            align-items: center;
            justify-content: center;
            direction: ltr;
            gap: 60px;
        }
        .rate input[type="radio"] {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .rate:not(:checked) > label {
            float:right;
            width:1em;
            overflow:hidden;
            white-space:nowrap;
            cursor:pointer;
            font-size:45px;
            color:#ccc;
            margin-bottom: -7px;
        }
        .rate:not(:checked) > label:before {
            content: '★ ';
        }
        .rate > input:checked ~ label {
            color: #ffc700;
        }
        .rate:not(:checked) > label:hover,
        .rate:not(:checked) > label:hover ~ label {
            color: #deb217;
        }


        @media (max-width: 640px) {
            body::before {
                height: 300px;
            }

            .form-card {
                padding: 40px 28px;
            }

            .form-title {
                font-size: 28px;
            }

            .form-subtitle {
                font-size: 15px;
            }

            .progress-steps {
                gap: 8px;
            }

            .progress-step {
                font-size: 11px;
                padding-top: 28px;
            }

            .progress-step::before {
                width: 28px;
                height: 28px;
                font-size: 12px;
            }

            .button-group {
                flex-direction: column-reverse;
            }

            button {
                width: 100%;
            }
            .rate
            {
                gap: 0;
            }
            .rate:not(:checked) > label
            {
                font-size: 30px;
            }
            .radio-group
            {
                flex-direction: column;
            }
        }
    </style>
@endpush

@section('content')


    @include('front.includes.inner-head', ['title' => 'التقييم العام'])


    <section class="bg-main-25 py-60 w-100 h-100">
        <div class="container container--lg">
            <div class="d-flex gap-24  z-2 position-relative">
                @include('front.auth.includes.sidebar')
                <div class="w-100">
                    <div class="mb-32">
                        <div class="d-flex align-items-center gap-16 justify-content-between">
                            <button type="button" class="toggle-student-dashbord-button  text-32 d-xl-none d-block">
                                <i class="ph-bold ph-list"></i>
                            </button>
                        </div>
                        <div class="my-24">

                            <div class="overflow-x-auto">
                                <div class="row mx-0">
                                    <div class="container">
                                        <div class="form-card">
                                            <div class="decorative-element"></div>
                                            <div class="form-content">
                                                <div class="form-header">
                                                    <h1 class="form-title">التقييم العام</h1>
                                                    <p class="form-subtitle">لكي تحصل علي تجربة افضل برجاء الاجابة بصراحة تامة وشفافية</p>
                                                </div>


                                                @if($already_evaluated)
                                                    <div class="success-message active">
                                                        <div class="success-icon"></div>
                                                        <h3>تم تقييم هذه الدورة التدريبة من قبل!</h3>
                                                        <p>تم التقييم بنجاح - يمكنك الآن الإطلاع على الشهادة الخاصه بهذه الدورة التدريبية من خلال (شهاداتي)</p>
                                                    </div>
                                                @else
                                                    <div class="progress-container">
                                                        <div class="progress-bar">
                                                            <div class="progress-fill" id="progressFill"></div>
                                                        </div>
                                                        <div class="progress-steps">
                                                            <span class="progress-step active" data-step="1" data-number="1">معلومات عامة</span>
                                                            @foreach($evaluation_categories as $index => $category)
                                                                <span class="progress-step" data-step="{{$index+2}}"
                                                                      data-number="{{$index+2}}">{{$category->name}}</span>
                                                            @endforeach
                                                        </div>
                                                    </div>

                                                    <div class="error-message" id="errorMessage"></div>

                                                    <form id="multiStepForm" action="{{route('front.auth.course.evaluation.submit',$course)}}" method="POST">
                                                        @csrf
                                                        <!-- Step 1 -->
                                                        <div class="form-step active" data-step="1">
                                                            <div class="form-group">
                                                                <label for="fullName">الأسم </label>
                                                                <input type="text" disabled id="fullName" placeholder="{{auth()->user()->name}}">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="machine_code">  الكود الوظيفي  </label>
                                                                <input type="text" disabled id="machine_code" placeholder="{{auth()->user()->machine_code}}">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="department"> القسم</label>
                                                                <input type="text" disabled id="department" placeholder="{{auth()->user()->department_name}}">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="course_name">اسم الكورس</label>
                                                                <input type="text" id="course_name"  placeholder="{{$course->title}}" disabled>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="instructor_id">اسم المحاضر <span class="required">*</span></label>
                                                                <select class="" name="instructor_id" id="instructor_id" required>
                                                                    @foreach($course->instructors as $instructor)
                                                                        <option value="{{$instructor->id}}">{{$instructor->name}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                        @foreach($evaluation_categories as $index => $category)
                                                            <div class="form-step" data-step="{{$index+2}}">
                                                                @foreach($category->evaluations as $evaluation)
                                                                    <div class="form-group">
                                                                        <label>{{$evaluation->title}} <span class="required">*</span></label>
                                                                        <div class="radio-group">
                                                                            @if($evaluation->type == 'five')
                                                                                <div class="rate">
                                                                                    @for ($i = 5; $i >= 1; $i--)
                                                                                        @if($i == 5)<div>(الأعلي)</div>@endif
                                                                                        <input type="radio" id="radio_{{$evaluation->id}}_{{$i}}" name="question[{{$evaluation->id}}]" value="{{ $i }}" />
                                                                                        <label for="radio_{{$evaluation->id}}_{{$i}}" title="{{ $i }} stars">
                                                                                            {{ $i }}
                                                                                        </label>
                                                                                        @if($i==1)<div>(الأقل)</div>@endif
                                                                                    @endfor
                                                                                </div>
                                                                            @elseif($evaluation->type == 'ten')
                                                                                @for($i=1; $i <= 10; $i++)
                                                                                    @if($i==1)<span>(الأقل)</span>@endif
                                                                                    <div class="radio-option ten">
                                                                                        <label for="radio_{{$evaluation->id}}_{{$i}}">{{$i}} </label>
                                                                                        <input type="radio" id="radio_{{$evaluation->id}}_{{$i}}"
                                                                                               name="question[{{$evaluation->id}}]" value="{{$i}}" required>
                                                                                    </div>
                                                                                    @if($i == 10)<span>(الأعلي)</span>@endif
                                                                                @endfor
                                                                            @else
                                                                                <textarea class="form-control mx-20" placeholder="الإجابه..." rows="2" cols="2" name="question[{{$evaluation->id}}]" required></textarea>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endforeach


                                                        <div class="button-group">
                                                            <button type="button" class="btn-secondary" id="prevBtn" style="display: none;">رجوع</button>
                                                            <button type="button" class="btn-primary" id="nextBtn">استمرار</button>
                                                        </div>
                                                    </form>

                                                    <div class="success-message" id="successMessage">
                                                        <div class="success-icon"></div>
                                                        <h3>شكراً لك!</h3>
                                                        <p>تم التقييم بنجاح - يمكنك الآن الإطلاع على الشهادة الخاصه بهذه الدورة التدريبية من خلال (شهاداتي)</p>
                                                    </div>
                                                @endif

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



@endsection

@push('js')
<script>
    var total_steps = {{$evaluation_categories->count() + 1}};
    let currentStep = 1;
    const totalSteps = total_steps;
    const formSteps = document.querySelectorAll('.form-step');
    const progressSteps = document.querySelectorAll('.progress-step');
    const progressFill = document.getElementById('progressFill');
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');
    const errorMessage = document.getElementById('errorMessage');
    const form = document.getElementById('multiStepForm');
    const successMessage = document.getElementById('successMessage');

    function updateProgress() {
        const progress = ((currentStep - 1) / (totalSteps - 1)) * 100;
        progressFill.style.width = progress + '%';

        progressSteps.forEach((step, index) => {
            const stepNum = index + 1;
            step.classList.remove('active', 'completed');
            if (stepNum < currentStep) {
                step.classList.add('completed');
            } else if (stepNum === currentStep) {
                step.classList.add('active');
            }
        });

        formSteps.forEach(step => {
            step.classList.remove('active');
            if (parseInt(step.dataset.step) === currentStep) {
                step.classList.add('active');
            }
        });

        prevBtn.style.display = currentStep === 1 ? 'none' : 'block';
        nextBtn.textContent = currentStep === totalSteps ? 'حفظ' : 'استمرار';

        errorMessage.classList.remove('active');
    }

    function validateStep() {
        const currentStepElement = document.querySelector(`.form-step[data-step="${currentStep}"]`);
        const requiredInputs = currentStepElement.querySelectorAll('[required]');

        for (let input of requiredInputs) {
            if (!input.value.trim()) {
                errorMessage.textContent = 'من فضلك املئ جميع الحقول مطلوبة';
                errorMessage.classList.add('active');
                input.focus();
                return false;
            }

            if (input.type === 'radio') {
                const radioGroup = currentStepElement.querySelectorAll(`[name="${input.name}"]`);
                const isChecked = Array.from(radioGroup).some(radio => radio.checked);
                if (!isChecked) {
                    errorMessage.textContent = 'من فضلك الحقل مطلوب';
                    errorMessage.classList.add('active');
                    return false;
                }
            }
        }

        return true;
    }


    function collectFormData() {
        const formData = new FormData(form);
        const data = {};
        formData.forEach((value, key) => {
            data[key] = value;
        });
        return data;
    }

    nextBtn.addEventListener('click', () => {
        if (!validateStep()) {
            return;
        }

        if (currentStep === totalSteps) {
            const formData = collectFormData();
            console.log('Form submitted:', formData);
            //ajax here
            var url = form.action;
            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if(response.status)
                    {
                        form.style.display = 'none';
                        document.querySelector('.progress-container').style.display = 'none';
                        document.querySelector('.button-group').style.display = 'none';
                        successMessage.classList.add('active');
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;

                        $('.error-text').remove(); // امسح القديم

                        $.each(errors, function (key, messages) {
                            let fieldName = key.replace('.', '_');

                            let input = $('[name="' + key + '"]');

                            if (input.length === 0) {
                                input = $('[name^="' + key.split('.')[0] + '"]');
                            }

                            input.closest('.form-group')
                                .append('<small class="error-text text-danger">' + messages[0] + '</small>');
                        });
                    }
                }
            })
        } else {
            currentStep++;
            updateProgress();
        }
    });

    prevBtn.addEventListener('click', () => {
        if (currentStep > 1) {
            currentStep--;
            updateProgress();
        }
    });

    form.addEventListener('keypress', (e) => {
        if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
            e.preventDefault();
            nextBtn.click();
        }
    });

    updateProgress();

</script>
@endpush
