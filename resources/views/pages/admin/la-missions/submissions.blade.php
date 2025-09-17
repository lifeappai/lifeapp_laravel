@extends('layouts.admin')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <style>

        .ui-datepicker .ui-datepicker-prev::before,
        .ui-datepicker .ui-datepicker-next::before {
            content: ''; 
            display: none;
        }
        .custom-thumbnail {
            min-width: 200px;
            height: 200px;
        }

        #flash-message {
            position: fixed;
            top: 10%;
            z-index: 100;
            right: 2%;
        }
    </style>
@endsection
@section('breadcrumb')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div>
            <h4 class="content-title mb-2"></h4>
            <nav aria-label="breadcrumb">
                <h4 class="content-title mb-2">{{ __('Submitted Missions') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Submitted Missions</li>
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
                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="card-title mg-b-0 mt-2">Mission Submissions ({{ $submittedMissions->total() }})</h4>
                        </div>
                        <div class="col-md-12 mt-3">
                            <form method="GET">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <input class="form-control fc-datepicker" placeholder="YYYY/MM/DD" type="text"
                                            @if ($date) value="{{ $date }}" @endif
                                            name="from_date" id="from_date">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <input class="form-control fc-datepicker" placeholder="YYYY/MM/DD" type="text"
                                            @if ($end_date) value="{{ $end_date }}" @endif
                                            name="end_date" id="end_date">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <select name="missionType" class="form-control" id="">
                                            <option value="all">All Missions</option>
                                            <option value="approved" @if ($missionType == 'approved') selected @endif>
                                                Approved Missions
                                            </option>
                                            <option value="requested" @if ($missionType == 'requested') selected @endif>
                                                Requested Missions
                                            </option>
                                            <option value="rejected" @if ($missionType == 'rejected') selected @endif>
                                                Rejected Missions
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <select name="assignedBy" class="form-control" id="">
                                            <option value="">Assigned By</option>
                                            <option value="teacher" @if (request('assignedBy') == 'teacher') selected @endif>
                                                Teacher
                                            </option>
                                            <option value="self" @if (request('assignedBy') == 'self') selected @endif>
                                                Self
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3 d-flex">
                                        <button type="submit" class="btn btn-success  mr-3">Search</button>
                                        <a href="{{ route('admin.la.missions.submissions') }}"
                                            class="btn btn-danger">Reset</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-2 mb-2">
                            <form method="get" action="{{ route('admin.la.missions.submissions') }}">
                                <input type="hidden" @if ($date) value="{{ $date }}" @endif
                                    name="from_date">
                                <input type="hidden" @if ($end_date) value="{{ $end_date }}" @endif
                                    name="end_date">
                                <input type="hidden" @if ($missionType) value="{{ $missionType }}" @endif
                                    name="missionType">
                                <input type="hidden" @if (request('assignedBy')) value="{{ request('assignedBy') }}" @endif 
                                    name="assignedBy">
                                <input type="hidden" name="type" value="export">
                                <button type="submit" data-toggle="tooltip" data-placement="top" title="export"
                                    class="btn btn-purple">Export</button>
                            </form>
                        </div>
                        <div class="col-md-2 mb-2">
                            <a href="{{ route('admin.la.missions.submissions.chart') }}" type="button"
                                data-toggle="tooltip" data-placement="top" title="Charts" class="btn btn-purple">View
                                Graph</a>
                        </div>
                    </div>
                </div>
                <div id="flash-message"></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example" class="table key-buttons text-md-nowrap data-table">
                            <thead>
                                <tr>
                                    <th>Sr No.</th>
                                    <th>Student Name</th>
                                    <th>Submission Date</th>
                                    <th>Mission Name</th>
                                    <th>Assigned By</th>
                                    <th>Image Submitted</th>
                                    <th>Allocated Coins</th>
                                    <th>Status</th>
                                    <th>Mobile Number</th>
                                    <th>Image</th>
                                    <th>School Name</th>
                                    <th>District Name</th>
                                    <th>Block Name</th>
                                    <th>Cluster Name</th>
                                    <th>State</th>
                                    <th>City</th>
                                    <th>Grade</th>
                                    <th>Subject</th>
                                    <th>Mission Id</th>
                                    <th>Total Coins</th>
                                    <th>Each Mission Timing</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = $submittedMissions->perPage() * ($submittedMissions->currentPage() - 1) + 1; ?>
                                @foreach ($submittedMissions as $submittedMission)
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td>{{ $submittedMission->user ? $submittedMission->user->name : '' }}</td>
                                        <td>{{ date('d-m-Y H:i:s', strtotime($submittedMission->created_at)) }}</td>
                                        <td>{{ $submittedMission->laMission ? (isset($submittedMission->laMission->default_title) ? $submittedMission->laMission->default_title : '') : '' }}
                                        </td>
                                        <td>{{ $submittedMission->teacher_name ?? 'Self' }}</td>
                                        <td>
                                            @if ($submittedMission->media)
                                                <a class="image-popup-no-margins"
                                                    href="{{ $imageBaseUrl }}{{ $submittedMission->media->path }}"><img
                                                        alt=""
                                                        src="{{ $imageBaseUrl }}{{ $submittedMission->media->path }}"
                                                        class="custom-thumbnail"></a>
                                            @endif
                                        </td>
                                        <td>
                                            <span
                                                class="allocated_coins-{{ $submittedMission->id }}">{{ $submittedMission->points }}</span>
                                        </td>
                                        <td>
                                            @if ($submittedMission->approved_at)
                                                Approved
                                            @elseif ($submittedMission->rejected_at)
                                                Rejected
                                            @else
                                                @php
                                                    $points = $submittedMission->laMission->getGamePoints() ?? 0;
                                                @endphp
                                                <div class="d-flex missionApproveDisapprove-{{ $submittedMission->id }}">
                                                    <button class="btn btn-success btn-sm"
                                                        onclick=missionApproveReject({{ $submittedMission->id }},1,{{ $points }},{{ $submittedMission->la_mission_id }})>
                                                        Approve </button>
                                                    <button class="ml-2 btn btn-danger btn-sm"
                                                        onclick=missionApproveReject({{ $submittedMission->id }},-1,{{ $points }},{{ $submittedMission->la_mission_id }})>
                                                        Reject </button>
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ $submittedMission->user ? $submittedMission->user->mobile_no : '' }}</td>
                                        <td>
                                            @php
                                                $userImage = $submittedMission->user ? ($submittedMission->user->image_path ? $imageBaseUrl . $submittedMission->user->image_path : '') : '';
                                            @endphp
                                            @if ($userImage)
                                                <a class="image-popup-no-margins" href="{{ $userImage }}">
                                                    <img alt="" src="{{ $userImage }} "
                                                        class="custom-thumbnail">
                                                </a>
                                            @endif
                                        </td>
                                        <td>{{ $submittedMission->user->school->name ?? '' }}</td>
                                        <td>{{ $submittedMission->user->school->district ?? '' }}</td>
                                        <td>{{ $submittedMission->user->school->block ?? '' }}</td>
                                        <td>{{ $submittedMission->user->school->cluster ?? '' }}</td>
                                        <td>{{ $submittedMission->user ? $submittedMission->user->state : '' }}</td>
                                        <td>{{ $submittedMission->user ? $submittedMission->user->city : '' }}</td>
                                        <td>{{ $submittedMission->user ? $submittedMission->user->grade : '' }}</td>
                                        <td>{{ $submittedMission->laMission->subject->default_title ?? '' }}</td>
                                        <td>{{ $submittedMission->la_mission_id ?? '-' }}</td>
                                        <td>
                                            {{ $submittedMission->laMission->getGamePoints() ?? '' }}
                                        </td>

                                        <td>
                                            {{ $submittedMission->timing }}
                                        </td>
                                    </tr>
                                    <?php $i++; ?>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $submittedMissions->appends(Request::all())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>

    <div class="modal fade" id="missionCompletedModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                    <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" id="missionCompletedForm" class="missionCompletedForm">
                        @method('PATCH')
                        @csrf
                        <br>
                        <div class="rating_div" id="rating_div">
                            Points:
                            <input type="number" name="points" id="points" class="form-control" required />
                        </div>
                        Comment:
                        <textarea name="comment" id="comment" class="form-control" required maxlength="255" cols="30"
                            rows="5"></textarea>
                        <input type="hidden" name="status" id="mission_status">
                        <br>
                        <button type="submit" class="btn btn-success">submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('assets/js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/parsley.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("#missionCompletedForm").parsley();
            $('.fc-datepicker').datepicker({
                showOtherMonths: true,
                selectOtherMonths: true,
                dateFormat: 'yy-mm-dd'
            });
        });

        function missionApproveReject(missionCompleteId, status, points, missionId) {
            var url = "{{ route('admin.la.missions.submissions.users.approve.reject', ':missionCompleteId') }}";
            url = url.replace(':missionCompleteId', missionCompleteId);
            if (status == "-1") {
                $("#mission_status").val('-1');
                $("#rating").attr("required", false);
                $("#rating_div").hide();
                $(".modal-title").html("Mission Reject Reason");
                $("#points").attr('required', false);
                if (missionId === 1) {
                    $("#comment").val(
                        'Upload correct image to complete "The Har Ghar Balloon Car" event.'
                    );
                } else {
                    $("#comment").val('Upload correct image and submit again');
                }
            } else {
                $("#mission_status").val('1');
                $("#rating").attr("required", true);
                $("#rating_div").show();
                $(".modal-title").html("Mission Approve Reason");
                $("#points").attr('required', true);
                $("#points").val(points);
                if (missionId === 1) {
                    $("#comment").val(
                        'Congratulations, you have successfully completed "The Har Ghar Balloon Car" event. Solve more missions to earn more Coins.'
                    );
                } else {
                    $("#comment").val('Well done. Mission Completed. Solve more missions to earn more Coins.');
                }
            }
            $("#missionCompletedForm").attr("action", url);
            $("#missionCompletedModal").modal('show');
            $("#points").attr('max', points);
        }

        $("#missionCompletedForm").submit(function(e) {
            e.preventDefault();
            var form = $(this);
            var actionUrl = form.attr('action');
            if ($(this).parsley().isValid()) {
                $.ajax({
                    type: "patch",
                    url: actionUrl,
                    data: form.serialize(),
                    success: function(data) {
                        $("#missionCompletedModal").modal('hide');
                        $("#points").val("");
                        $("#comment").val("");
                        // alert(data.message);
                        $('#flash-message').html('<div class="alert alert-success">' + data.message +
                            '</div>');
                        setTimeout(function() {
                            $('#flash-message').slideUp('slow');
                        }, 3000);
                        if (data.status == 200) {
                            $(".missionApproveDisapprove-" + data.submit_mission_id).html(data
                                .mission_status);
                            $(".allocated_coins-" + data.submit_mission_id).html(data.allocated_coins);
                        }
                    }
                });
            }
        });
    </script>
@endsection
