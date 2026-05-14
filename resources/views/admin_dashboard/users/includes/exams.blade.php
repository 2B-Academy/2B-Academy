<div class="table-responsive mt-4">
    <table class="table align-middle table-hover">
        <thead class="table-secondary">
        <tr>
            <th>#</th>
            <Th>الدورة التدريبية</Th>
            <th>الأختبار</th>
            <th>نوع الأختبار</th>
            <th>الدرجة</th>
            <th>النجاح</th>
            <th>التحكم</th>
        </tr>
        </thead>
        <tbody>
        @forelse($exams as $con)
            <tr>
                <td>{{$con->id}}</td>
                <td>{{$con->course?->title}}</td>
                <td>{{$con->exam?->title}}</td>
                <td>{{$con->exam?->is_final ? 'اختبار نهائي' : 'اختبار تجريبي'}}</td>
                <td>
                    <div class="text-center bg-orange text-white bolder fw-bold rounded p-2">
                        <div>{{$con->user_degree}}</div>
                        <div>-------</div>
                        <div>{{$con->exam?->degree}}</div>
                    </div>
                </td>
                <td>
                    <span class="text-white badge {{$con->status == 'success' ? 'bg-success' : 'bg-danger'}}">{{$con->status == 'success' ? 'ناجح' : 'غير ناجح'}}</span>
                </td>
                <td>
                    <a href="{{ route('admin.user.user-exam.destroy', $con->id) }}" class="delete course_button"><i
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
