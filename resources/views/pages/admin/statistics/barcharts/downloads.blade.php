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
                    <li class="breadcrumb-item active" aria-current="page">Bar-Graphs</li>
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
                            <form action="{{ url()->current() }}">
                                <input type="hidden" name="type" value="monthly_count">
                                <button class="form-control btn btn-success">Month-Wise Count Graph</button>
                            </form>
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="col-md-6">
                    <h4 class="card-title mg-b-0 mt-2">Total Downloads({{ $totalCount['total_download_count'] }})</h4>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div style="width: 80%; margin: auto;">
                                    <canvas id="barChart2" width="15000"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <h4 class="card-title mg-b-0 mt-2">Grade-Wise Graph</h4>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div style="width: 80%; margin: auto;">
                                    <canvas id="grade_count" width="15000"></canvas>
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
        var myChart2 = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($totalCount['labels'] ?? ''),
                datasets: @json($totalCount['datasets'] ?? '')
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
            }
        });

        var ctx = document.getElementById('grade_count').getContext('2d');
        var myChart2 = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($gradeCountData['labels'] ?? ''),
                datasets: @json($gradeCountData['datasets'] ?? '')
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
@endsection
