<div class="table-responsive mt-4">
    <table class="table align-middle table-hover">
        <thead class="table-secondary">
        <tr>
            <th>#</th>
            <Th>الدورة التدريبية</Th>
            <th>السؤال</th>
            <th width="30%">الإجابة</th>
            <th>من قام بالاجابة</th>
            <th>التحكم</th>
        </tr>
        </thead>
        <tbody>
        @forelse($questions as $con)
            <tr>
                <td>{{$con->id}}</td>
                <td>{{$con->course?->title}} - {{$con->lecture?->title}}</td>
                <td>{{$con->question}}</td>
                <td>
                    <form method="post" id="addAnswer" action="{{route('admin.user.lecture-question.update', $con->id)}}">
                        @csrf
                        <textarea rows="2" cols="2" class="form-control" name="answer" placeholder="الرد على السؤال" required>{{$con->answer}}</textarea>
                        <button type="submit" class="mt-2 btn btn-success btn-sm">حفظ</button>
                    </form>
                </td>
                <td>{{$con->answeredBy?->name}}</td>
                <td>
                    <a href="{{ route('admin.user.lecture-question.destroy', $con->id) }}" class="delete course_button"><i
                            class="bi bi-trash-fill mx-1"></i> حذف</a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center">
                    <p>لا يوجد بيانات</p>
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
