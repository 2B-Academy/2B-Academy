<div class="col-12">
    <h4>أسئة اختبار : {{$content->title}}</h4>
    <div class="table-responsive mt-4">
        <table class="table align-middle table-hover">
            <thead class="table-secondary">
            <tr>
                <th>#</th>
                <Th>السؤال</Th>
                <Th>النوع</Th>
                <th>التحكم</th>
            </tr>
            </thead>
            <tbody>
            @forelse($content->questions as $con)
                <tr>
                    <td>{{$con->id}}</td>
                    <td>{{$con->question}}</td>
                    <td>
                        <span class="badge bg-{{$con->type == 'text' ? 'dark' : 'success'}}">{{$con->type}}</span>
                    </td>
                    <td>
                        <div class="table-actions d-flex align-items-center gap-3 fs-6">
                            <a href="{{route('admin.forms.question.destroy', $con->id)}}" class="delete text-danger" data-bs-toggle="tooltip" data-bs-placement="bottom"
                               title="حذف"><i class="bi bi-trash-fill"></i></a>

                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">
                        <p>لا يوجد أسئلة حتي الآن</p>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
