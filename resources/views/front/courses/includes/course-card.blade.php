<div class="course-item bg-white rounded-16 p-12 h-100 box-shadow-md">
    <div class="course-item__thumb rounded-12 overflow-hidden position-relative">
        <span class="course_type_label fw-medium text-neutral-700">{{ $course->course_type == 'offline' ? 'أوفلاين 🔴' : 'أونلاين 🟢' }}</span>
        <a href="{{route('front.course.details', [$course->id, $course->slug])}}" class="w-100 h-100">
            <img src="{{$course->getFileUrl($course->image)}}" alt="{{$course->title}}" class="course-item__img rounded-12 cover-img transition-2">
        </a>
    </div>
    <div class="course-item__content position-relative">

        <div class="">
            <div class="mb-16 flex-align gap-16 flex-wrap">
                <a href="{{route('front.course.details', [$course->id, $course->slug])}}" class="py-8 px-20 rounded-pill flex-align gap-8 text-main-600 fw-medium bg-main-25 hover-bg-main-600 hover-text-white">
                                                <span class="text-xl d-flex">
                                                    <i class="ph-bold ph-squares-four"></i>
                                                </span>
                    {{$course->category->name}}
                </a>

            </div>
            <h4 class="mb-28">
                <a href="{{route('front.course.details', [$course->id, $course->slug])}}" class="link text-line-2">{{$course->title}}</a>
            </h4>
            <div class="flex-align gap-28 flex-wrap mb-16">
                <div class="flex-align gap-8">
                    <span class="text-neutral-700 text-2xl d-flex"><i class="ph-bold ph-video-camera"></i></span>
                    <span class="text-neutral-700 text-lg fw-medium">{{$course->lectures_count}}</span>
                </div>
                <div class="flex-align gap-8">
                    <span class="text-neutral-700 text-2xl d-flex"><i class="ph-bold ph-clock"></i></span>
                    <span class="text-neutral-700 text-lg fw-medium">{{$course->hours}} ساعة</span>
                </div>
            </div>
        </div>
        <div class="flex-between gap-8 pt-24 border-top border-neutral-50 mt-28 border-dashed border-0">
            <h4 class="mb-0 text-main-two-600">{{$course->price > 0 ? $course->price.' '.$course->currency : 'مجاناً'}}</h4>
            <div class="flex-align gap-4">
                <span class="text-2xl fw-medium text-warning-600 d-flex"><i class="ph-fill ph-star"></i></span>
                <span class="text-lg text-neutral-700 fw-medium">
                                               {{number_format($course->ratings_avg_rating, 2)}}
                                                <span class="text-neutral-100 fw-normal">({{$course->ratings_count}})</span>
                                            </span>
            </div>
        </div>
    </div>
</div>
