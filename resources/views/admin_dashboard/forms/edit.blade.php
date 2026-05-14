@extends('admin_dashboard.layout.master')
@section('Page_Title')   الأختبارات العامة | تعديل   @endsection
@push('css')
    <style>
        #radio_answers,#yes_no_answers
        {
            background: #e3e3e3;
            margin-top: 20px !important;
            padding: 25px;
            border-radius: 15px;
        }
        input[type='radio']
        {
            width: 35px;
            height: 35px;
            border-radius: 4px !important;
            cursor: pointer;
        }
    </style>
@endpush
@section('content')

    <div class="row">
        <div class="col-lg-12 mx-auto">
            <div class="breadcrumb d-flex align-items-center justify-content-between">
                <div class="">
                    <a class="text-dark" href="{{route('admin.forms.index')}}">الأختبارات العامة</a>
                    <span class="mx-2">-</span>
                    <strong class="text-primary"> {{$content->title}}</strong>
                    <span class="mx-2">-</span>
                    <strong class="text-primary">الأسئلة </strong>
                </div>
            </div>
            <div class="card">
                <div class="card-body">

                    <div class="row g-3 mt-4">
                        <div class="col-12">
                            <div class="card shadow-none bg-light border">
                                <div class="card-body">
                                    <form class="row g-3" id="validateForm" method="post" enctype="multipart/form-data"
                                          action="{{route('admin.forms.update', $content->id)}}">
                                        @method('put')
                                        @csrf
                                        <div class="col-md-12">
                                            <label class="form-label">  السؤال <span class="text-danger">*</span> </label>
                                            <input type="text" name="question" class="form-control" required>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label">  نوع السؤال <span class="text-danger">*</span> </label>
                                            <select class="form-control form-select" name="type" required id="question_type">
                                                <option value="radio">Radio</option>
                                                <option value="yes_no">Yes / No</option>
                                                <option value="text">Text</option>
                                            </select>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="row m-0" id="radio_answers">
                                                <div class="col-12 m-3">
                                                    <strong>اكتب إجابات السؤال واختر الإجابة الصحيحة بينهم : </strong>
                                                </div>
                                                @for ($i = 0; $i < 4; $i++)
                                                    <div class="col-md-6 mb-2">
                                                        <div class="d-flex align-items-center gap-2 px-3">
                                                            <input type="radio" class="form-check-input"
                                                                   name="radio_answer_check[is_true]" value="{{$i}}"
                                                                   required>
                                                            <input type="text" name="radio_answer[{{ $i }}]"
                                                                   class="form-control"
                                                                   placeholder="الإجابة {{$i + 1}}" required>
                                                        </div>
                                                    </div>
                                                @endfor
                                            </div>

                                            <div class="row m-0 d-none" id="yes_no_answers">
                                                <div class="col-12 m-3">
                                                    <strong>اكتب إجابات السؤال واختر الإجابة الصحيحة بينهم : </strong>
                                                </div>
                                                @for ($i = 0; $i < 2; $i++)
                                                    <div class="col-md-6 mb-2">
                                                        <div class="d-flex align-items-center gap-2 px-3">
                                                            <input type="radio" class="form-check-input"
                                                                   name="yes_no_answer_check[is_true]" value="{{$i}}">
                                                            <input type="text" name="yes_no_answer[{{ $i }}]"
                                                                   class="form-control" value="{{$i == 0 ? 'نعم' : 'لا'}}"
                                                                   placeholder="الإجابة {{$i + 1}}">
                                                        </div>
                                                    </div>
                                                @endfor
                                            </div>
                                        </div>

                                        @include('admin_dashboard.inputs.add_btn')
                                    </form>
                                </div>
                            </div>
                        </div>

                        @include('admin_dashboard.forms._questions')

                    </div><!--end row-->
                </div>
            </div>
        </div>
    </div>

@endsection

@push('js')
    @include('admin_dashboard.components.delete')
    <script>
        $(document).on('change', '#question_type', function () {
            $('#radio_answers, #yes_no_answers').addClass('d-none');
            $('#radio_answers input, #yes_no_answers input').prop('required', false);
            if (this.value === 'radio') {
                $('#radio_answers').removeClass('d-none');
                $('#radio_answers input').prop('required', true);
            }
            else if (this.value === 'yes_no') {
                $('#yes_no_answers').removeClass('d-none');
                $('#yes_no_answers input').prop('required', true);
            }
        });
    </script>

@endpush
