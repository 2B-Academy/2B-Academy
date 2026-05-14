@extends('admin_dashboard.layout.master')
@section('Page_Title')  ERRoRs @endsection

@section('content')


    <div class="card">
        <div class="card-body">

            <div class="d-flex align-items-center">
                <h5 class="mb-0"> <i class="bi bi-grid-fill"></i>  ERRoRs </h5>
            </div>

            <div class="table-responsive mt-4">
                <table class="table align-middle table-hover">
                    <thead class="table-secondary">
                    <tr>
                        <th>URL</th>
                        <Th>Method</Th>
                        <th>Error Message</th>
                        <th>Line</th>
                        <th>Created At</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($content as $con)
                        <tr>
                            <td>{{$con->url}}</td>
                            <td>
                                <span class="badge  {{$con->method == 'GET' ? 'bg-success' : 'bg-danger' }}">{{$con->method}}</span>
                            </td>
                            <td>{{$con->message}}</td>
                            <td>{{$con->line}}</td>
                            <td>{{date('Y-m-d H:i A', strtotime($con->created_at))}}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">
                                <p>No Data Found</p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                <div>
                    {{$content->links()}}
                </div>
            </div>
        </div>
    </div>


@endsection
