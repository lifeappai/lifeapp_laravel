@extends('layouts.admin')
@section('breadcrumb')
    <div class="breadcrumb-header justify-content-between">
        <div>
            <h4 class="content-title mb-2"></h4>
            <nav aria-label="breadcrumb">
                <h4 class="content-title mb-2">{{ __('Queries') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Queries</li>
                </ol>
            </nav>
        </div>
    </div>
    <div id="error">
        @include('includes.message')
    </div>
@endsection
@section('css')
    <style>
        .custom-thumbnail {
            width: 30px;
            height: 30px;
            border-radius: 50%;
        }
    </style>
@endsection
@section('content')
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card mg-b-20">
                <div class="card-header pb-0">
                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="card-title mg-b-0 mt-2">Queries List ({{ $queries->total() }})</h4>
                        </div>
                        <div class="col-md-12 mt-3">
                            <div class="row">
                                <div class="col-md-12">
                                    <form method="get" action="{{ route('admin.queries.index') }}">
                                        <div class=" d-flex row mb-3">
                                            <div class="col-md-3 mb-3">
                                                <select name="status" class="form-control" id="">
                                                    <option value="">Waiting Reply</option>
                                                    <option value="{{ App\Models\LaQuery::STATUS_OPEN }}"
                                                        @if ($request->status != null && $request->status == App\Models\LaQuery::STATUS_OPEN) selected @endif>Open</option>
                                                    <option value="{{ App\Models\LaQuery::STATUS_CLOSED }}"
                                                        @if ($request->status == App\Models\LaQuery::STATUS_CLOSED) selected @endif>Closed</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <select name="mentor_id" class="form-control" id="">
                                                    <option value="">All</option>
                                                    @if (isset($mentors) && count($mentors) > 0)
                                                        @foreach ($mentors as $mentor)
                                                            <option value="{{ $mentor->id }}"
                                                                @if ($request->mentor_id == $mentor->id) selected @endif>
                                                                {{ $mentor->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <button type="submit" class="btn btn-success">Search</button>
                                                <a href={{ route('admin.queries.index') }} class="btn btn-warning">clear</a>
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
                        <table id="example" class="table key-buttons text-md-nowrap">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0">Sr No.</th>
                                    <th class="border-bottom-0">Query</th>
                                    <th class="border-bottom-0">Student Name</th>
                                    <th class="border-bottom-0">Mentor Name</th>
                                    <th class="border-bottom-0">Subject</th>
                                    <th class="border-bottom-0">Rating</th>
                                    <th class="border-bottom-0">Feedback</th>
                                    <th class="border-bottom-0">Status</th>
                                    <th class="border-bottom-0">Action</th>
                                </tr>
                            </thead>
                            <?php $i = $queries->perPage() * ($queries->currentPage() - 1) + 1; ?>
                            <tbody>
                                @if (count($queries) > 0)
                                    @foreach ($queries as $query)
                                        <tr class="user_list">
                                            <td>{{ $i }}</td>
                                            <td>{{ $query->description }}</td>
                                            <td>{{ $query->createdBy ? $query->createdBy->name : '-' }}</td>
                                            <td>{{ $query->mentor ? $query->mentor->name : '-' }}</td>
                                            <td>{{ $query->subject ? $query->subject->main_title : '-' }}</td>
                                            <td>{{ $query->rating ? $query->rating : '-' }}</td>
                                            <td>{{ $query->feedback ? $query->feedback : '-' }}</td>
                                            <td>{{ ucfirst($query->status) }}</td>
                                            <td><a href="{{ route('admin.queries.replies', $query->id) }}"><button
                                                        class="btn btn-success">View</button></a>
                                            </td>
                                        </tr>
                                        <?php $i++; ?>
                                    @endforeach
                                @else
                                    <td class="text-center" colspan="7">No Queries Found </td>
                                @endif
                            </tbody>
                        </table>
                        @if (isset($queries) && count($queries) > 0)
                            {{ $queries->appends(Request::all())->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
@endsection
@section('js')
@endsection
