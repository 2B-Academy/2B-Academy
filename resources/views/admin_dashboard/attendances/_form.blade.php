<div class="my-3 d-flex align-items-start justify-content-start gap-2">
    <h6 class="text-primary">{{$course?->title}}</h6>
    <span class="mx-1 text-primary">-</span>
    <h6 class="text-primary">{{$section?->name}}</h6>
    <span class="mx-1 text-primary">-</span>
    <h6 class="text-primary">عدد المحاضرات : {{$group_sessions_count}}</h6>
</div>
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>#</th>
            <th>كود الموظف</th>
            <th>الأسم</th>
            @for($i = 1 ; $i <= $group_sessions_count ; $i++)
                <th> محاضرة : {{$i}}</th>
            @endfor
        </tr>
        </thead>
        <tbody>
        @forelse($users as $user)
            <tr>
                <td>
                    <button class="btn btn-sm btn-dark compare-attendance-dates" data-user="{{$user->user_id}}"
                            data-section="{{$section?->id}}">
                        <i class="bx bx-git-compare mx-1"></i> مقارنة المواعيد
                    </button>
                </td>
                <td>{{$user->user?->machine_code}}</td>
                <td>{{$user->user?->name}}</td>
                @for($i = 1 ; $i <= $group_sessions_count ; $i++)
                    <td>
                        <div class="form-check form-switch my-3">
                            <input class="form-check-input attendance-toggle customSliderCheckbox" type="checkbox"
                                   name="attendance"
                                   value="1"
                                   data-course="{{$user->course_id}}"
                                   data-user="{{$user->user_id}}"
                                @checked($i <= ($user->user?->attendances_count ?? 0))>
                        </div>
                    </td>
                @endfor
            </tr>
        @empty
            <tr>
                <td colspan="3" class="text-center">لا يوجد موظفين علي هذه المجموعة حتي الآن</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>



<!-- Appointment Modal Modal -->
<div class="modal fade" id="appointmentTable" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5>
                    <i class="bx bx-git-compare mx-1"></i> مقارنة المواعيد
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row">
                <div class="col-12">
                    <div class="d-flex align-items-center justify-content-around userInfoHeader">
                        <h6>  <span id="codeName"></span> </h6>
                        <h6>  <span id="userName"></span> </h6>
                        <h6>  <span id="courseName">{{$course?->title}}</span> </h6>
                        <h6>  <span id="sectionName">{{$section?->name}}</span> </h6>
                    </div>
                    <div class="my-3 d-flex align-items-center justify-content-around attendanceInfoHeader">
                        <h6> عدد مرات الحضور : <span id="attendance_count"></span> </h6>
                        <h6> العدد الكلي للمحاضرات : <span id="sessions_count"></span> </h6>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="user-appointments">
                        <table class="table table-bordered table-hover text-center">
                            <thead>
                            <tr>
                                <th>موعد تسجيل الحضور</th>
                            </tr>
                            </thead>
                            <tbody id="user-appointments"></tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="sessions-appointments">
                        <table class="table table-bordered table-hover text-center">
                            <thead>
                            <tr>
                                <th> الأسم</th>
                                <th>موعد المحاضرة</th>
                                <th>من</th>
                                <th>إلي</th>
                            </tr>
                            </thead>
                            <tbody id="sessions-appointments"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
