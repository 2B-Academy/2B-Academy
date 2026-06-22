@extends('admin_dashboard.layout.master')
@section('Page_Title')   {{ __('text.qualification_skills') }} | {{ __('text.create') }}   @endsection

@section('content')

    <div class="row">
        <div class="col-lg-12 mx-auto">
            <div class="breadcrumb d-flex align-items-center justify-content-between">
                <div class="">
                    <a class="text-dark" href="{{route('admin.qualification-skills.index')}}">{{ __('text.qualification_skills') }}</a>
                    <span class="mx-2">-</span>
                    <strong class="text-primary">{{ __('text.create') }}</strong>
                </div>
            </div>
            <div class="card">
                <div class="card-body">

                    <div class="row g-3 mt-4">
                        <div class="col-12">
                            <div class="card shadow-none bg-light border">
                                <div class="card-body">
                                    <form class="row g-3" id="validateForm" method="post"
                                          action="{{route('admin.qualification-skills.store')}}">
                                        @csrf
                                        @include('admin_dashboard.qualification-skills._form')
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div><!--end row-->
                </div>
            </div>
        </div>
    </div>

@endsection
