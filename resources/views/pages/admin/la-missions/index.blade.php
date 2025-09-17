@extends('layouts.admin')
@section('css')
@endsection
@section('breadcrumb')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div>
            <h4 class="content-title mb-2"></h4>
            <nav aria-label="breadcrumb">
                <h4 class="content-title mb-2">{{ __('Missions') }} ({{ $missions->total() }})</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Missions</li>
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
                <div class="card-header pb-0">
                    <div class="d-flex">
                        <div class="col-md-10">
                            <form method="GET">
                                <div class="d-flex">
                                    <div class="col-md-3">
                                        <select name="la_subject_id" class="form-control">
                                            <option value="">Select subject</option>
                                            @foreach ($subjects as $key => $subject)
                                                <option value="{{ $subject->id }}"
                                                    @if ($subjectId == $subject->id) selected @endif>
                                                    {{ $subject->default_title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="status" class="form-control">
                                            <option value="">Select Status</option>
                                            <option value="{{ App\Enums\StatusEnum::ACTIVE }}"
                                                @if (isset($statusId) && $statusId == App\Enums\StatusEnum::ACTIVE) selected @endif>Published</option>
                                            <option value="{{ App\Enums\StatusEnum::DEACTIVE }}"
                                                @if (isset($statusId) && $statusId == App\Enums\StatusEnum::DEACTIVE) selected @endif>Drafted</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="type" class="form-control">
                                            <option value="">Select Type</option>
                                            <option value="{{ App\Enums\GameType::MISSION }}"
                                                @if ($type == App\Enums\GameType::MISSION) selected @endif>Mission</option>
                                            <option value="{{ App\Enums\GameType::JIGYASA }}"
                                                @if ($type == App\Enums\GameType::JIGYASA) selected @endif>Jigyasa</option>
                                            <option value="{{ App\Enums\GameType::PRAGYA }}"
                                                @if ($type == App\Enums\GameType::PRAGYA) selected @endif>Pragya</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 d-flex">
                                        <button type="submit" class="btn btn-success  mr-3">Search</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-5">
                        <form method="get" action="">
                            <div class="d-flex">
                                <div class="col-md-10"></div>
                                <div class="col-md-2">
                                    <a class="form-control btn btn-success"
                                        href="{{ route('admin.la.missions.create', request()->all()) }}">Add Mission</a>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table id="example" class="table key-buttons text-md-nowrap">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0" scope="col">Subject</th>
                                    <th class="border-bottom-0" scope="col">Level</th>
                                    <th class="border-bottom-0" scope="col">Type</th>
                                    <th class="border-bottom-0" scope="col">Title</th>
                                    <th class="border-bottom-0" scope="col">Status</th>
                                    <th class="border-bottom-0" scope="col">Index</th>
                                    <th class="border-bottom-0" scope="col">Action</th>
                                </tr>
                            </thead>
                            <?php $i = 1; ?>
                            <tbody>
                                @if (count($missions) > 0)
                                    @foreach ($missions as $mission)
                                        <tr>
                                            <td>{{ $mission->subject ? $mission->subject->default_title : '' }}</td>
                                            <td>{{ $mission->laLevel ? $mission->laLevel->default_title : '' }}</td>
                                            <td>
                                                @if ($mission->type == App\Enums\GameType::MISSION)
                                                    Mission
                                                @elseif($mission->type == App\Enums\GameType::JIGYASA)
                                                    Jigyasa
                                                @elseif($mission->type == App\Enums\GameType::PRAGYA)
                                                    Pragya
                                                @endif
                                            </td>
                                            <td>
                                                @if (count($mission->title) > 0)
                                                    @foreach ($mission->title as $key => $title)
                                                        {{ $key }} => {{ $title }} <br>
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td>
                                                @if (App\Enums\StatusEnum::DEACTIVE == $mission->status)
                                                    <button type="button"
                                                        class="btn btn-sm btn-danger waves-effect waves-light"
                                                        onclick="sweetAlertAjax('PATCH','{{ route('admin.la.missions.status', $mission->id) }}', 'Are You Sure To Publish')">Draft</button>
                                                @else
                                                    <button type="button"
                                                        class="btn btn-sm btn-success waves-effect waves-light"
                                                        onclick="sweetAlertAjax('PATCH','{{ route('admin.la.missions.status', $mission->id) }}', 'Are You Sure To Draft')">Publish</button>
                                                @endif
                                            </td>
                                            <td>
                                                <input type="text" class="form-control index-{{ $mission->id }}"
                                                    id="index" name="index" value="{{ $mission->index }}"
                                                    data-id="{{ $mission->id }}"
                                                    onkeyup="this.value=this.value.replace(/[^0-9]/g, '')">
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    <a href="{{ route('admin.la.missions.edit', $mission->id) }}"><button
                                                            class='btn btn-purple btn-sm'> Edit </button></a>
                                                    <a href="{{ route('admin.la.missions.resources', $mission->id) }}"><button
                                                            class='btn btn-pink ml-2 btn-sm'> Resources </button></a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td class="text-center" colspan="10">No Data Found </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                        {{ $missions->appends(Request::all())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>

    <div class="modal fade" id="descriptionModel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Descripiton</h4>
                </div>
                <div class="modal-body">
                    <div class="w-100" id="descriptionModelInfo">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('assets/js/parsley.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("#missionForm").parsley()
        });

        $(document).on('change', '#index', function(event) {
            var laMissionId = $(this).data("id");
            var url = "{{ route('admin.la.missions.index.sequence', ':laMissionId') }}";
            url = url.replace(':laMissionId', laMissionId);
            $.ajax({
                url: url,
                type: "PATCH",
                data: {
                    _token: "{{ csrf_token() }}",
                    'index': this.value,
                },
                cache: false,
                beforeSend: function() {},
                success: function(html) {
                    $(".index-" + laMissionId).val(html.index);
                    alert(html.message);
                }
            });
        });
    </script>
@endsection
