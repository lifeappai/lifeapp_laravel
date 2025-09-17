@extends('layouts.admin')
@section('css')
    <style>
    </style>
@endsection
@section('breadcrumb')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div>
            <h4 class="content-title mb-2"></h4>
            <nav aria-label="breadcrumb">
                <h4 class="content-title mb-2">{{ __('Push Notification Campaigns') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Push Notification Campaigns</li>
                </ol>
            </nav>
        </div>
    </div>
    <div id="error">
        @include('includes.message')
    </div>
@endsection
@section('content')
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card mg-b-20">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title mg-b-0 mt-2"></h4>
                        <i class="mdi mdi-dots-horizontal text-gray"></i>

                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-5">
                        <form method="get" action="">
                            <div class="d-flex">
                                <div class="col-md-10"></div>
                                <div class="col-md-2">
                                    <a class="form-control btn btn-success"
                                        href="{{ route('admin.push.notification.campaigns.create') }}">Add
                                        Campaign</a>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table id="example" class="table key-buttons text-md-nowrap data-table">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0" scope="col">Sr No.</th>
                                    <th class="border-bottom-0" scope="col">Name</th>
                                    <th class="border-bottom-0" scope="col">Title</th>
                                    <th class="border-bottom-0" scope="col">Body</th>
                                    <th class="border-bottom-0" scope="col">School</th>
                                    <th class="border-bottom-0" scope="col">State</th>
                                    <th class="border-bottom-0" scope="col">City</th>
                                    <th class="border-bottom-0" scope="col">Scheduled Date Time</th>
                                    <th class="border-bottom-0" scope="col">Total Users</th>
                                    <th class="border-bottom-0" scope="col">Success Users</th>
                                    <th class="border-bottom-0" scope="col">Failed Users</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($campaigns) > 0)
                                    <?php $i = $campaigns->perPage() * ($campaigns->currentPage() - 1) + 1; ?>
                                    @foreach ($campaigns as $campaign)
                                        <tr>
                                            <td>{{ $i }}</td>
                                            <td>{{ $campaign->name }}</td>
                                            <td>{{ $campaign->title }}</td>
                                            <td>{{ $campaign->body }}</td>
                                            <td>{{ $campaign->school ? $campaign->school->name : null }}</td>
                                            <td>{{ $campaign->state }}</td>
                                            <td>{{ $campaign->city }}</td>
                                            <td>{{ $campaign->scheduled_at }}</td>
                                            <td>{{ count($campaign->users) }}</td>
                                            <td>{{ count($campaign->success_users) }}</td>
                                            <td>{{ count($campaign->failed_users) }}</td>
                                        </tr>
                                        <?php $i++; ?>
                                    @endforeach
                                @else
                                    <tr>
                                        <td class="text-center" colspan="10">No Data Found </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                        {{ $campaigns->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    </div>
@endsection
@section('js')
    <script></script>
@endsection
