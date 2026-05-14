<div class="table-responsive mt-4">
    <table class="table align-middle table-hover">
        <thead class="table-secondary">
        <tr>
            <th>#</th>
            <Th>الدورة التدريبية</Th>
            <th>التقييم</th>
            <th>التعليق</th>
            <th>التاريخ</th>
            <th>التحكم</th>
        </tr>
        </thead>
        <tbody>
        @forelse($ratings as $con)
            <tr>
                <td>{{$con->id}}</td>
                <td>{{$con->course?->title}}</td>
                <td>
                    <div class="d-flex gap-2">
                        @for($i = 1; $i <= (int)$con->rating ; $i++)
                            <span class="text-15 fw-medium text-main d-flex" id="{{$i}}"><i class="lni lni-star-filled text-warning"></i></span>
                        @endfor
                    </div>
                </td>
                <td>{{$con->comment}}</td>
                <td>{{date('Y-m-d H:i A', strtotime($con->updated_at))}}</td>
                <td>
                    <a href="{{ route('admin.user.ratings.destroy', $con->id) }}" class="delete course_button"><i
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
