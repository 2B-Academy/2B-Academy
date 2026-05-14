@forelse($course->sections as $section)
    <div class="accordion-item">
        <h2 class="accordion-header">
            <button class="accordion-button {{$loop->first ? '' : 'collapsed'}}" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapse_{{$section->id}}" aria-expanded="{{$loop->first ? 'true' : 'false'}}" aria-controls="collapse_{{$section->id}}">
                {{$section->name}}
            </button>
        </h2>
        <div id="collapse_{{$section->id}}" class="accordion-collapse collapse {{$loop->first ? 'show' : ''}}" data-bs-parent="#accordionExampleTwo">
            <div class="accordion-body p-0">
                @foreach($section->lectures as $lecture)
                    <a href="{{($enrolled) ? route('front.course.lecture', [$course, $lecture]) : 'javascript:void(0)'}}"
                       @if(!\Illuminate\Support\Facades\Auth::check()) onclick="userCantAccessLecture('auth')" @elseif(!$enrolled) onclick="userCantAccessLecture('enrolled')"  @endif class="curriculam-item flex-between gap-16 text-neutral-500 fw-medium hover-text-main-600">
                        <span class="flex-align gap-12">
                            <i class="text-xl d-flex ph-bold ph-video-camera"></i>
                            <span class="text-line-1">{{$lecture->title}}</span>
                        </span>
                            <span class="flex-align gap-12 flex-shrink-0">
                            <i class="text-xl d-flex ph-bold ph-video-camera"></i>
                        </span>
                    </a>
                @endforeach
                @foreach($section->exams as $exam)
                    <a  href="{{($enrolled) ? route('front.course.exam', [$course, $exam]) : 'javascript:void(0)'}}"
                        @if(!\Illuminate\Support\Facades\Auth::check()) onclick="userCantAccessLecture('auth')" @elseif(!$enrolled) onclick="userCantAccessLecture('enrolled')"  @endif class="curriculam-item flex-between gap-16 text-neutral-500 fw-medium hover-text-main-600">
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
