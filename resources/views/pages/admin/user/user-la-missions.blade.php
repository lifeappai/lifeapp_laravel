@extends('layouts.admin')
@section('breadcrumb')
    <div class="breadcrumb-header justify-content-between">
        <div>
            <h4 class="content-title mb-2"></h4>
            <nav aria-label="breadcrumb">
                <h4 class="content-title mb-2">{{ __('Users') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.list') }}">Users</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $user->name }}</li>
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
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title mg-b-0 mt-2"></h4>
                        <i class="mdi mdi-dots-horizontal text-gray"></i>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <img class="img-fluid"
                                style="border-radius: 100%;height: 140px;width: 140px;border: 3px solid #ff9501;"
                                src="{{ $user->image_path ? $imageBaseUrl . $user->image_path : '' }}">
                        </div>
                        <div class="col-md-10">
                            <p><b> {{ $user->name }}</b></p>
                            <p><b>Address:</b> {{ $user->address }},{{ $user->city }},{{ $user->state }}</p>
                            <p><b>School:</b> {{ $user->school ? $user->school->name : '-' }}</p>
                            <p> {{ $user->grade }} grade</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example" class="table key-buttons text-md-nowrap data-table">
                            <thead>
                                <tr>
                                    <th>Sr No.</th>
                                    <th>Submission Date</th>
                                    <th>Mission Name</th>
                                    <th>Image Submitted</th>
                                    <th>Allocated Coins</th>
                                    <th>Total Coins</th>
                                    <th>Each Mission Timing</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    <input type="hidden" id="userId" value="{{ $user->id }}">
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
                        <textarea name="comment" id="comment" class="form-control" required maxlength="255" cols="30" rows="5"></textarea>
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
    <script src="{{ asset('assets/js/parsley.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $("#missionCompletedForm").parsley()
        });

        function missionApproveReject(missionCompleteId, status, points) {
            var url = "{{ route('admin.la.missions.submissions.users.approve.reject', ':missionCompleteId') }}";
            url = url.replace(':missionCompleteId', missionCompleteId);
            if (status == "-1") {
                $("#mission_status").val('-1');
                $("#rating").attr("required", false);
                $("#rating_div").hide();
                $(".modal-title").html("Mission Reject Reason");
                $("#points").attr('required', false);
            } else {
                $("#mission_status").val('1');
                $("#rating").attr("required", true);
                $("#rating_div").show();
                $(".modal-title").html("Mission Approve Reason");
                $("#points").attr('required', true);
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
                        alert(data.message);
                        if (data.status == 200) {
                            $(".missionApproveDisapprove-" + data.submit_mission_id).html(data
                                .mission_status);
                            $(".allocated_coins-" + data.submit_mission_id).html(data.allocated_coins);
                        }
                    }
                });
            }
        });

        $(document).ready(function() {
            var userId = $("#userId").val();
            var url = "{{ route('admin.users.la.missions', ':userId') }}";
            url = url.replace(":userId", userId);
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": url,
                    "type": "GET",
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'submission_date',
                        name: 'submission_date'
                    },
                    {
                        data: 'mission_name',
                        name: 'mission_name'
                    },
                    {
                        data: 'image_submitted',
                        orderable: false,
                        name: 'image_submitted'
                    },
                    {
                        data: 'allocated_coins',
                        name: 'allocated_coins'
                    },
                    {
                        data: 'total_coins',
                        name: 'total_coins'
                    },
                    {
                        data: 'user_timings',
                        name: 'user_timings'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                ],
                "autoWidth": false,
                "fnDrawCallback": function() {
                    $(".image-popup-no-margins").magnificPopup({
                        type: "image",
                        closeOnContentClick: !0,
                        closeBtnInside: !1,
                        fixedContentPos: !0,
                        mainClass: "mfp-no-margins mfp-with-zoom",
                        image: {
                            verticalFit: !0
                        },
                        zoom: {
                            enabled: !0,
                            duration: 300
                        }
                    });
                }
            });
        });
    </script>
@endsection
