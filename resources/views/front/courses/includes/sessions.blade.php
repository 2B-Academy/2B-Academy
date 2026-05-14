@forelse($course->sections as $section)
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button {{$loop->first ? '' : 'collapsed'}}" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapse_{{$section->id}}" aria-expanded="{{$loop->first ? 'true' : 'false'}}" aria-controls="collapse_{{$section->id}}">
                    {{$section->name}}
                    @if($section->id == $my_group)
                        <small class="btn btn-sm btn-dark mx-5">أنت مسجل علي هذه المجموعة</small>
                    @endif
                </button>
            </h2>
            <div id="collapse_{{$section->id}}" class="accordion-collapse collapse {{$loop->first ? 'show' : ''}}" data-bs-parent="#accordionExampleTwo">
                <div class="accordion-body p-0">
                    @if($section->id == $my_group)
                        @foreach($section->sessions as $lecture)
                            <a href="javascript:void(0)" class="curriculam-item flex-between gap-16 text-neutral-500 fw-medium hover-text-main-600">
                        <span class="flex-align gap-12">
                            <i class="text-xl d-flex ph-bold ph-video-camera"></i>
                            <span class="text-line-1">{{$lecture->title}}</span>
                        </span>
                                <span class="flex-align gap-12 flex-shrink-0">
                            <span class="text-line-1">{{date("Y-m-d", strtotime($lecture->session_date))}}</span>
                        </span>
                                <span class="flex-align gap-12 flex-shrink-0">
                             <span class="text-line-1">
                                 <div class="d-flex align-items-center">
                                     <span>{{date('h:i A', strtotime($lecture->time_from))}}</span>
                                     <span class="mx-2"> - </span>
                                     <span>{{date('h:i A', strtotime($lecture->time_to))}}</span>
                                 </div>
                             </span>
                        </span>
                            </a>
                        @endforeach
                    @endif
                    @foreach($section->exams as $exam)
                        <a  href="{{($enrolled) ? route('front.course.exam', [$course, $exam]) : 'javascript:void(0)'}}"
                            @if(!\Illuminate\Support\Facades\Auth::check()) onclick="userCantAccessLecture('auth')"  @endif class="curriculam-item flex-between gap-16 text-neutral-500 fw-medium hover-text-main-600">
                    <span class="flex-align gap-12">
                        <i class="text-xl d-flex ph-bold ph-question"></i>
                        <span class="text-line-1">{{$exam->title}}</span>
                    </span>
                            <span class="flex-align gap-12 flex-shrink-0">
                        <i class="text-xl d-flex ph-bold ph-question"></i>
                    </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
@empty
    @include('front.includes.noData')
@endforelse
