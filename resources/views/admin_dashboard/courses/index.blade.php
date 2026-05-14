@extends('admin_dashboard.layout.master')
@section('Page_Title')
    الدورات التدريبية
@endsection

@section('content')
    <div class="card">
        <div class="card-body">

            <div class="d-flex align-items-center">
                <h5 class="mb-0"> <i class="bi bi-grid-fill"></i> الدورات التدريبية </h5>
                <div class="ms-auto position-relative">
                    <a href="{{ route('admin.courses.create') }}" class="btnIcon btn btn-outline-primary px-5"><i
                            class="lni lni-circle-plus"></i> إنشاء </a>
                </div>
            </div>

            <div class="row">
                <!--filter -->
                <div class="col-12">
                    <form method="GET" action="" class="years my-4 py-3 d-flex align-items-center justify-content-start gap-3 overflow-auto">
                        <div class="form-group w-100">
                            <label for="from">الأقسام</label>
                            <select class="form-control form-select select2" name="category_id">
                                <option value="">اختر القسم</option>
                                @foreach($categories as $category)
                                    <option @selected($category->id == request('category_id')) value="{{$category->id}}">{{$category->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group w-100">
                            <label class="form-label"> ابحث بأسم الكورس</label>
                            <input type="text" class="form-control" name="name" value="{{ request('name') }}" placeholder="ابحث بأسم الكورس">
                        </div>
                        <div class="form-group w-100">
                            <button type="submit" class="btn btn-main mt-3 w-50">فلتر</button>
                            <a class="btn btn-dark mt-3" href="{{route('admin.courses.index')}}">إلغاء</a>
                        </div>

                    </form>
                </div>

            </div>

            <div class="table-responsive mt-4">
                <table class="table align-middle table-hover">
                    <thead class="table-secondary">
                        <tr>
                            <th>#</th>
                            <Th width="30%">الأسم</Th>
                            <th>النوع</th>
                            <th>الحالة</th>
                            <th>التحكم</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($content as $con)
                            <tr>
                                <td>{{ $con->id }}</td>
                                <td>{{ $con->title }}</td>
                                <td>{{ $con->course_type == 'offline' ? 'أوفلاين 🔴' : 'أونلاين 🟢' }}</td>
                                <td>
                                    <strong
                                        class="mx-2 badge @if ($con->active) bg-light-success text-success @else bg-light-danger text-danger @endif">{{ $con->active ? 'نشط' : 'غير نشط' }}</strong>
                                </td>
                                <td>
                                    <div class="table-actions d-flex align-items-center gap-3 fs-6">
                                        <a href="{{ route('admin.courses.sections.index', $con->id) }}" class="course_button sections"><i
                                                class="lni lni-layers mx-1"></i> {{$con->course_type == 'offline' ? 'المجموعات' : 'السكاشن'}}</a>
                                        <a href="{{ route('admin.courses.resources.index', $con->id) }}" class="course_button resources"><i
                                            class="lni lni-link mx-1"></i> المصادر </a>
                                        <a href="{{ route('admin.courses.assignments.index', $con->id) }}" class="course_button assignments"><i
                                                class="lni lni-link mx-1"></i> المهام </a>
                                        @if($con->course_type == 'offline')
                                            <a href="{{ route('admin.courses.sessions.index', $con->id) }}" class="course_button lectures"><i
                                                    class="lni lni-video mx-1"></i> المواعيد </a>
                                        @else
                                            <a href="{{ route('admin.courses.lectures.index', $con->id) }}" class="course_button lectures"><i
                                                    class="lni lni-video mx-1"></i> المحاضرات </a>
                                        @endif
                                        <a href="{{ route('admin.courses.exams.index', $con->id) }}" class="course_button exams"><i
                                                class="lni lni-write mx-1"></i> الأختبارات</a>
                                        <a href="{{ route('admin.courses.edit', $con->id) }}" class="course_button edit"><i
                                                class="bi bi-pencil-fill mx-1"></i> تعديل</a>

                                        <a href="{{ route('admin.courses.destroy', $con->id) }}" class="delete course_button"><i
                                                class="bi bi-trash-fill mx-1"></i> حذف</a>

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
                <div>
                    {{ $content->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    @include('admin_dashboard.components.delete')
@endpush
