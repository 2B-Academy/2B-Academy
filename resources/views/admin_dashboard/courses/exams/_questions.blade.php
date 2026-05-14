<div class="col-12 py-3">
    <button type="button" class="btn btn-sm btn-icon btn-success"
            id="addNewQuestion"><i class="lni lni-plus"></i></button>
</div>
<div class="row m-0" id="rows">
    @if($content->id > 0)
        @foreach($content->questions as $index => $question)
            <div class="col-12 question-box mb-3" id="row">
                <div class="d-flex align-items-center justify-content-around gap-4">
                    <div class="row m-0">
                        <div class="col-12 mb-3">
                            <input type="text" class="form-control py-3" name="questions[{{$index}}][title]"
                                   placeholder="السؤال" value="{{$question->question}}" required>
                            <small class="text-primary">اختر الإجابة الصحيحة ما بين الإجابات قبل الحفظ (يمكنك الإكتفاء بإجابتين عالأقل)</small>
                        </div>
                        @foreach($question->answers as $i => $answer)
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center gap-2 px-3">
                                    <input type="radio" class="form-check-input" name="questions[{{$index}}][is_correct]"
                                           value="{{ $i }}" @checked($answer->is_correct) @if($i==0 || $i==1) required @endif>
                                    <input type="text" name="questions[{{$index}}][answers][{{ $i }}]" class="form-control"
                                           value="{{$answer->answer}}" placeholder="الإجابة {{$i + 1}}"  @if($i==0 || $i==1) required @endif>
                                </div>
                            </div>
                        @endforeach
                        @if(count($question->answers) <= 2)
                            @for ($i = 2; $i <= 3; $i++)
                                <div class="col-md-6 mb-2">
                                    <div class="d-flex align-items-center gap-2 px-3">
                                        <input type="radio" class="form-check-input" name="questions[{{$index}}][is_correct]"
                                               value="{{ $i }}">
                                        <input type="text" name="questions[{{$index}}][answers][{{ $i }}]" class="form-control"
                                               value="" placeholder="الإجابة {{$i + 1}}">
                                    </div>
                                </div>
                            @endfor
                        @endif
                    </div>
                    <button type="button" id="removeRow"
                            class="btn btn-sm btn-icon btn-danger">
                        <i class="lni lni-close"></i>
                    </button>
                </div>
            </div>
        @endforeach
    @else
    <div class="col-12 question-box mb-3" id="row">
        <div class="d-flex align-items-center justify-content-around gap-4">
            <div class="row m-0">
                <div class="col-12 mb-3">
                    <input type="text" class="form-control" name="questions[0][title]"
                           placeholder="السؤال" value="" required>
                    <small class="text-primary">اختر الإجابة الصحيحة ما بين الإجابات قبل الحفظ (يمكنك الإكتفاء بإجابتين عالأقل)</small>
                </div>
                @for ($i = 0; $i < 4; $i++)
                    <div class="col-md-6 mb-2">
                        <div class="d-flex align-items-center gap-2 px-3">
                            <input type="radio" class="form-check-input" name="questions[0][is_correct]" value="{{ $i }}" @if($i==0 || $i==1) required @endif>
                            <input type="text" name="questions[0][answers][{{ $i }}]" class="form-control" placeholder="الإجابة {{$i + 1}}" @if($i==0 || $i==1) required @endif>
                        </div>
                    </div>
                @endfor
            </div>
            <button type="button" id="removeRow"
                    class="btn btn-sm btn-icon btn-danger">
                <i class="lni lni-close"></i>
            </button>
        </div>
    </div>
    @endif

</div>
