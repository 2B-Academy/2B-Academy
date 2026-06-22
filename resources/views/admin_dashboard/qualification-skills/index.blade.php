@extends('admin_dashboard.layout.master')
@section('Page_Title')  {{ __('text.qualification_skills') }} @endsection

@section('content')


    <div class="card">
        <div class="card-body">

            <div class="d-flex align-items-center">
                <h5 class="mb-0"> <i class="bi bi-grid-fill"></i>  {{ __('text.qualification_skills') }} </h5>
                <div class="ms-auto position-relative">
                    <a href="{{route('admin.qualification-skills.create')}}" class="btnIcon btn btn-outline-primary px-5"><i class="lni lni-circle-plus"></i> {{ __('text.create') }} </a>
                </div>
            </div>

            <form action="{{route('admin.qualification-skills.index')}}" method="GET" class="gap-3 d-lg-flex align-items-center justify-content-center mt-3 mb-2">
                <input type="text" name="search" class="form-control" value="{{request('search')}}" placeholder="{{ __('text.search_placeholder') }}">
                <button type="submit" class="btn btn-primary w-50">
                    <i class="lni lni-search mx-1"></i> {{ __('text.search') }}
                </button>
            </form>

            <div class="table-responsive mt-4">
                <table class="table align-middle table-hover">
                    <thead class="table-secondary">
                    <tr>
                        <th>#</th>
                        <th>{{ __('text.name_ar') }}</th>
                        <th>{{ __('text.name_en') }}</th>
                        <th>{{ __('text.courses_count') }}</th>
                        <th>{{ __('text.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($content as $con)
                        <tr>
                            <td>{{$con->id}}</td>
                            <td>{{ $con->getTranslation('name', 'ar', false) }}</td>
                            <td>{{ $con->getTranslation('name', 'en', false) }}</td>
                            <td>{{ $con->courses_count ?? 0 }}</td>
                            <td>
                                <div class="table-actions d-flex align-items-center gap-3 fs-6">
                                    <a href="{{route('admin.qualification-skills.edit', $con->id)}}" class="text-primary" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                       title="{{ __('text.edit') }}"><i class="bi bi-pencil-fill"></i></a>

                                    <a href="{{route('admin.qualification-skills.destroy', $con->id)}}" class="delete text-danger" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                       title="{{ __('text.delete') }}"><i class="bi bi-trash-fill"></i></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">
                                <p>{{ __('text.no_records') }}</p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                <div>
                    {{ $content->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>


@endsection

@push('js')
    @include('admin_dashboard.components.delete')
@endpush
