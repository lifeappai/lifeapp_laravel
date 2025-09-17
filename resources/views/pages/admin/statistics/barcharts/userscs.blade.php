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
                <h4 class="content-title mb-2">{{ __('Teachers') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Bar-Charts</li>
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
                    <div class="row">
                        <div class="col-md-3">
                            <a class="form-control btn btn-success"
                                href="{{ route('admin.chhattisgarh.district.status') }}">Total Downloads</a>
                        </div>
                    </div>
                    <hr>
                    <div class="mb-5">
                        <form method="get" action="{{ route('admin.bargraph.users') }}">
                            <div class="d-flex">
                                <div class="col-md-3 mb-3">
                                    <select name="is_life_lab" id="is_life_lab" class="form-control">
                                        <option value="all" @if ($request->is_life_lab == 'all') selected @endif>All Student
                                        </option>
                                        <option value="{{ App\Enums\StatusEnum::YES }}"
                                            @if ($request->is_life_lab == App\Enums\StatusEnum::YES) selected @endif>Life-lab Student</option>
                                        <option value="{{ App\Enums\StatusEnum::NO }}"
                                            @if ($request->is_life_lab === (string) App\Enums\StatusEnum::NO) selected @endif>Other Student</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-success">Search</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <canvas id="barChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div style="width: 100%; margin: auto;">
                                    <canvas id="barChart2" width="15000"></canvas>
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
    <script src="{{ asset('assets/js/Chart.bundle.min.js') }}"></script>
    <script>
        var ctx = document.getElementById('barChart2').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($data['labels']),
                datasets: @json($data['datasets'])
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
            }
        });
    </script>

    <script>
        var barChartData = <?php echo json_encode($barChart); ?>;
        var labels = [];
        var counts = [];
        var colors = [];

        barChartData.forEach(function(item) {
            labels.push(item.label);
            counts.push(item.count);
            colors.push(item.color);
        });

        var barCtx = document.getElementById('barChart').getContext('2d');
        var barChart = new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'User Counts',
                    data: counts,
                    backgroundColor: colors,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'User Counts by Category'
                    }
                }
            }
        });
    </script>
@endsection
