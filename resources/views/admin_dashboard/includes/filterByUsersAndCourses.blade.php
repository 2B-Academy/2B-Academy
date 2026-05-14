<form method="GET" action="" class="col-12">
    <div class="row m-0">
        @if($courses)
        <div class="col-md-{{$answered_filter ? '4' : '6'}} mb-2">
            <label for="course">الدورة التدريبية:</label>
            <select name="course_id" id="course" class="form-control select2" onchange="this.form.submit()">
                <option value="">كل الدورات التدريبية</option>
                @foreach($allCourses as $id => $title)
                    <option value="{{ $id }}" {{ $selectedCourse == $id ? 'selected' : '' }}>
                        {{ $title }}
                    </option>
                @endforeach
            </select>
        </div>
        @endif
        @if($users)
        <div class="col-md-{{$answered_filter ? '4' : '6'}} mb-2">
            <label for="user">الموظف:</label>
            <select name="user_id"  id="allUsers" class="form-control" onchange="this.form.submit()">
            </select>
        </div>
        @endif
        @if($answered_filter)
        <div class="col-md-4 mb-2">
            <label for="user">فلتر بالمجاب والغير مجاب:</label>
            <select name="answered" id="answered" class="form-control" onchange="this.form.submit()">
                <option value="">الكل</option>
                <option value="yes" {{ $answered == 'yes' ? 'selected' : '' }}>
                    الأسئلة المجابة
                </option>
                <option value="no" {{ $answered == 'no' ? 'selected' : '' }}>
                    الأسئلة الغير المجابة
                </option>
            </select>
        </div>
        @endif
    </div>
</form>
