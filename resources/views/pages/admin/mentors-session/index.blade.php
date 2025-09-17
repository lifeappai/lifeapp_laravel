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
                <h4 class="content-title mb-2">{{ __('Mentors Sessions') }} ({{ $laSessions->total() }})</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Mentor Sessions</li>
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
                    <div class="row">
                        <div class="col-md-12 mt-3">
                            <div class="row">
                                <div class="col-md-12">
                                    <form method="get" action="{{ url()->current() }}">
                                        <div class=" d-flex row mb-3">
                                            <div class="col-md-3 mb-3">
                                                <select name="status" class="form-control" id="status">
                                                    <option value="" selected>Select Status</option>
                                                    <option value="{{ App\Enums\StatusEnum::ACTIVE }}"
                                                        {{ $request->status && $request->status == App\Enums\StatusEnum::ACTIVE ? 'selected' : '' }}>
                                                        Active
                                                    </option>
                                                    <option value="{{ App\Enums\StatusEnum::DEACTIVE }}"
                                                        {{ $request->status && $request->status == App\Enums\StatusEnum::DEACTIVE ? 'selected' : '' }}>
                                                        Inactive
                                                    </option>

                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <button type="submit" class="btn btn-success">Search</button>
                                                <button href={{ url()->current() }} class="btn btn-warning"
                                                    onclick="clearFilter()">clear</a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example" class="table key-buttons text-md-nowrap data-table">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0" scope="col">Sr No.</th>
                                    <th class="border-bottom-0" scope="col">Mentor Name</th>
                                    <th class="border-bottom-0" scope="col">Session Heading</th>
                                    <th class="border-bottom-0" scope="col">Session Date-Time</th>
                                    <th class="border-bottom-0" scope="col">Zoom Password</th>
                                    <th class="border-bottom-0" scope="col">Status</th>
                                    <th class="border-bottom-0" scope="col">Action</th>
                                </tr>
                            </thead>
                            <?php $i = $laSessions->perPage() * ($laSessions->currentPage() - 1) + 1; ?>
                            <tbody>
                                @if (count($laSessions) > 0)
                                    @foreach ($laSessions as $laSession)
                                        <tr class="user_list">
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $laSession->user->name }}</td>
                                            <td>{{ $laSession->heading }}</td>
                                            <td>{{ date('d-m-Y H:i:s', strtotime($laSession->date_time)) }}</td>
                                            <td>{{ $laSession->zoom_password }}</td>
                                            <td>
                                                @if ($laSession->status == App\Enums\StatusEnum::ACTIVE)
                                                    <button type="button"
                                                        class="btn btn-sm btn-success waves-effect waves-light"
                                                        onclick="sweetAlertAjax('PATCH','{{ route('admin.la.sessions.status', $laSession->id) }}', 'Are You Sure To Inactive')">Active</button>
                                                @else
                                                    <button type="button"
                                                        class="btn btn-sm btn-danger waves-effect waves-light"
                                                        onclick="sweetAlertAjax('PATCH','{{ route('admin.la.sessions.status', $laSession->id) }}', 'Are You Sure To Active')">Deactive</button>
                                                @endif
                                            </td>
                                            <td>
                                                <a class="mr-2"
                                                    href="{{ route('admin.la.sessions.edit', $laSession->id) }}">
                                                    <button type="button" data-toggle="tooltip" data-placement="top"
                                                        title="edit" class="btn btn-purple btn-sm">
                                                        Edit
                                                    </button>
                                                </a>
                                                <a class="mr-2"
                                                    href="{{ route('admin.la.participants.index', $laSession->id) }}">
                                                    <button type="button" data-toggle="tooltip" data-placement="top"
                                                        title="edit" class="btn btn-primary btn-sm">
                                                        Participants
                                                    </button>
                                                </a>
                                            </td>

                                        </tr>
                                    @endforeach
                                @else
                                    <td class="text-center" colspan="7">No Session Found </td>
                                @endif
                            </tbody>
                        </table>
                        @if (count($laSessions) > 0)
                            {{ $laSessions->appends(Request::all())->links() }}
                        @endif
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
    <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
    <script>
        function clearFilter() {
            $('#status').val('').trigger('change');
        }
    </script>
@endsection
