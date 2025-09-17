@extends('layouts.admin')
@section('breadcrumb')
    <div class="breadcrumb-header justify-content-between">
        <div>
            <h4 class="content-title mb-2"></h4>
            <nav aria-label="breadcrumb">
                <h4 class="content-title mb-2">{{ __('Users') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Users</li>
                </ol>
            </nav>
        </div>
    </div>
    <div id="error">
        @include('includes.message')
    </div>
@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}">
    <style>
        .custom-thumbnail {
            width: 30px;
            height: 30px;
            border-radius: 50%;
        }

        .canvas-container {
            width: 80%;
            margin: 20px auto;
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
                            <h4 class="card-title mg-b-0 mt-2">User List Graph</h4>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="canvas-container">
                        <div class="row">
                            <div class="col-md-12 mb-2 text-center">
                                <canvas id="school_chart"></canvas>
                                <label>School Wise Chart</label>
                                <hr>
                            </div>

                            <div class="col-md-12 mb-2 text-center">
                                <canvas id="coupon_chart"></canvas>
                                <label>Coupon Wise Chart</label>
                            </div>
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
<script>
    var allCharts = <?php echo json_encode($charts); ?>;
    $.each(allCharts, function(chartKey, chartValue) {
        console.log(chartValue);

        var counting = chartValue.counting;
        var names = chartValue.name;
        var colorCodes = chartValue.color;

        var stateCtx = document.getElementById(chartKey).getContext('2d');
        var statePieChart = {
            labels: names,
            datasets: [{
                label: 'No. of states: ',
                fill: false,
                data: counting,
                backgroundColor: "rgba(209,32,49,1)",
                backgroundColor: colorCodes,
            }],
            options: {
                animations: {
                    tension: {
                        duration: 1000,
                        easing: 'linear',
                        from: 1,
                        to: 0,
                        loop: true
                    }
                },

                responsive: true,
                maintainAspectRation: true,

                title: {
                    display: true,
                    text: 'Pie Chart'
                }
            }
        }

        const stateConfig = {
            type: 'pie',
            data: statePieChart,
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            },
        };
        var chart = new Chart(stateCtx, stateConfig);
    });
</script>
@endsection
