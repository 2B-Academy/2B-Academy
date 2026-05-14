<div class="table-responsive mt-4">
    <table class="table align-middle table-hover">
        <thead class="table-secondary">
        <tr>
            <th>#</th>
            <Th>الدورة التدريبية</Th>
            <th>نسبة التقدم</th>
        </tr>
        </thead>
        <tbody>
        @forelse($courses as $con)
            <tr>
                <td>{{$con->id}}</td>
                <td>{{$con->title}}</td>
                <td>
                    <div class="progress" style="height:18px;">
                        <div class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width: {{$con->user_progress}}%" aria-valuenow="{{$con->user_progress}}" aria-valuemin="0" aria-valuemax="100">
                        </div>
                        <span class="percentage">{{$con->user_progress}}%</span>
                    </div>
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
