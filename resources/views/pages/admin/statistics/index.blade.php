@extends('layouts.admin')
@section('css')
    <style>
        .anychart-credits {
            display: none;
        }
    </style>
@endsection
@section('breadcrumb')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div>
            <h4 class="content-title mb-2"></h4>
            <nav aria-label="breadcrumb">
                <h4 class="content-title mb-2">{{ __('Statistics') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Statistics</li>
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
                                    <form method="get" action="{{ route('admin.statistics.index') }}">
                                        <div class=" d-flex row mb-3">
                                            <div class="col-md-3 mb-3">
                                                <select name="state" id="state" class="form-control"
                                                    onchange="setCities(this.value);">
                                                    <option value="">Select State</option>
                                                    @if (count($states) > 0)
                                                        @foreach ($states as $stateData)
                                                            <option value="{{ $stateData }}"
                                                                {{ $request->state == $stateData ? 'selected' : '' }}>
                                                                {{ $stateData }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <select name="city" id="city" class="form-control">
                                                    <option value="">Select City</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <select name="school_id" class="form-control" id="">
                                                    <option value="">Select School</option>
                                                    @foreach ($schools as $school)
                                                        <option value="{{ $school->id }}"
                                                            {{ $request->school_id == $school->id ? 'selected' : '' }}>
                                                            {{ $school->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <select name="days" class="form-control" id="">
                                                    <option value="">From beginning</option>
                                                    <option value="7"
                                                        @if ($request->days == '7') selected @endif>Last 7 days
                                                    </option>
                                                    <option value="15"
                                                        @if ($request->days == '15') selected @endif>Last 15 days
                                                    </option>
                                                    <option value="30"
                                                        @if ($request->days == '30') selected @endif>Last 30 days
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <button type="submit" class="btn btn-success">Search</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mt-3">
                            <h4>User Wise Mission Chart:</h4>
                            <div id="user_wise_mission_chart" class="mx-auto" style="width: 275px; height: 275px"></div>
                        </div>
                        <div class="col-md-6 mt-3">
                            <h4>User Wise Quiz Chart:</h4>
                            <div id="user_wise_quiz_chart" class="mx-auto" style="width: 275px; height: 275px"></div>
                        </div>
                        <div class="col-md-6 mt-3">
                            <h4>Mission Chart:</h4>
                            <div id="mission_chart" class="mx-auto" style="width: 275px; height: 275px"></div>
                        </div>
                        <div class="col-md-6 mt-3">
                            <h4>Accurancy Chart:</h4>
                            <div id="accurancy_chart" class="mx-auto" style="width: 275px; height: 275px"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    </div>
    <input type="hidden" id="select_city" value="{{ $request->city }}">
@endsection
@section('js')
    <script src="https://cdn.anychart.com/releases/8.0.1/js/anychart-core.min.js"></script>
    <script src="https://cdn.anychart.com/releases/8.0.1/js/anychart-pie.min.js"></script>
    <script>
        var allCharts = <?php echo json_encode($charts); ?>;

        function setCities(state) {
            $("#city").html('<option value="">Select City</option>');
            var url = "{{ route('admin.cities', ':state') }}";
            url = url.replace(':state', state);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: url,
                type: "get",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "city": $("#select_city").val(),
                },
                success: function(data) {
                    $("#city").html(data);
                }
            });
        }

        $.each(allCharts, function(chartKey, chartValue) {
            anychart.onDocumentReady(function() {
                var data = chartValue;
                console.log(data);
                var chart = anychart.pie();
                chart.data(data);
                chart.container(chartKey);
                chart.legend(false);
                chart.normal().stroke('3px white');
                chart.explode(0);
                chart.animation(true);
                chart.animation().duration(1000);
                chart.draw();
            });
        });

        $(document).ready(function() {
            if ($("#sstate").val() != "") {
                setCities($("#state").val())
            }
        });
    </script>
@endsection
