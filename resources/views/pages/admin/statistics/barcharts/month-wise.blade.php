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
                <div class="col-md-6">
                    <h4 class="card-title mg-b-0 mt-2" style="display: inline;">Month-Wise Graph</h4>
                    <h4 class="card-title mg-b-0 mt-2" id="total_count" style="display: inline;">
                        ({{ $monthlyData['total_count'] }})</h4>
                </div>
                <br>
                <div class="mb-5">
                    <div class="d-flex">
                        <div class="col-md-3 mb-3">
                            <input type="hidden" name="type" value="monthly_count">
                            @php
                                $currentDate = now();
                                $currentMonthYear = $currentDate->format('Y-m');
                            @endphp
                            <input class="form-control fc-datepicker" placeholder="YYYY/MM" type="month"
                                @if ($currentMonthYear) value="{{ $currentMonthYear }}" @endif name="month_year"
                                id="month_year">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div style="width: 80%; margin: auto;">
                                    <canvas id="monthlyCountBar" width="15000"></canvas>
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
        var ctx = document.getElementById('monthlyCountBar').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($monthlyData['labels'] ?? ''),
                datasets: @json($monthlyData['datasets'] ?? '')
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                    }
                },
            }
        });

        $('#month_year').change(function() {
            var monthYear = $(this).val();
            $.ajax({
                url: "{{ url()->current() }}",
                type: 'GET',
                data: {
                    type: 'monthly_count',
                    month_year: monthYear
                },
                success: function(response) {
                    console.log(response);
                    updateChart(response);
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        });

        function updateChart(data) {
            console.log(data);
            myChart.data.labels = data.labels;
            data.datasets.forEach((dataset, index) => {
                myChart.data.datasets[index].data = dataset.data;
            });
            myChart.update();
            $('#total_count').text('(' + data.total_count + ')');
        }
    </script>
@endsection
