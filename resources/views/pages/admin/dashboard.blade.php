@extends('layouts.admin')

@section('css')
    <style>
        .custom-thumbnail {
            width: 30px;
            height: 30px;
            border-radius: 50%;
        }

        .projects-stat .table th,
        .projects-stat .table td {
            width: 120px;
        }

        .projects-stat .table-bordered th,
        .projects-stat .table-bordered td {
            border-bottom: 1px solid #dedcfb !important;
            text-align: center;
        }

        .table-bordered {
            border: 1px solid #dedcfb !important;
        }
    </style>
@endsection
@section('breadcrumb')
    <div class="breadcrumb-header justify-content-between">
        <div>
            <h4 class="content-title mb-2">Hi, welcome back!</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Project</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex my-auto">
            <div class=" d-flex right-page">
            </div>
        </div>
    </div>
    <!-- /breadcrumb -->
@endsection
@section('content')
    <!-- main-content-body -->
    <div class="main-content-body">
        <div class="row row-sm">
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card overflow-hidden project-card">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="my-auto">
                                <svg enable-background="new 0 0 469.682 469.682" version="1.1"
                                    class="mr-4 ht-60 wd-60 my-auto primary" viewBox="0 0 469.68 469.68"
                                    xml:space="preserve" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="m120.41 298.32h87.771c5.771 0 10.449-4.678 10.449-10.449s-4.678-10.449-10.449-10.449h-87.771c-5.771 0-10.449 4.678-10.449 10.449s4.678 10.449 10.449 10.449z" />
                                    <path
                                        d="m291.77 319.22h-171.36c-5.771 0-10.449 4.678-10.449 10.449s4.678 10.449 10.449 10.449h171.36c5.771 0 10.449-4.678 10.449-10.449s-4.678-10.449-10.449-10.449z" />
                                    <path
                                        d="m291.77 361.01h-171.36c-5.771 0-10.449 4.678-10.449 10.449s4.678 10.449 10.449 10.449h171.36c5.771 0 10.449-4.678 10.449-10.449s-4.678-10.449-10.449-10.449z" />
                                    <path
                                        d="m420.29 387.14v-344.82c0-22.987-16.196-42.318-39.183-42.318h-224.65c-22.988 0-44.408 19.331-44.408 42.318v20.376h-18.286c-22.988 0-44.408 17.763-44.408 40.751v345.34c0.68 6.37 4.644 11.919 10.449 14.629 6.009 2.654 13.026 1.416 17.763-3.135l31.869-28.735 38.139 33.959c2.845 2.639 6.569 4.128 10.449 4.18 3.861-0.144 7.554-1.621 10.449-4.18l37.616-33.959 37.616 33.959c5.95 5.322 14.948 5.322 20.898 0l38.139-33.959 31.347 28.735c3.795 4.671 10.374 5.987 15.673 3.135 5.191-2.98 8.232-8.656 7.837-14.629v-74.188l6.269-4.702 31.869 28.735c2.947 2.811 6.901 4.318 10.971 4.18 1.806 0.163 3.62-0.2 5.224-1.045 5.493-2.735 8.793-8.511 8.361-14.629zm-83.591 50.155-24.555-24.033c-5.533-5.656-14.56-5.887-20.376-0.522l-38.139 33.959-37.094-33.959c-6.108-4.89-14.79-4.89-20.898 0l-37.616 33.959-38.139-33.959c-6.589-5.4-16.134-5.178-22.465 0.522l-27.167 24.033v-333.84c0-11.494 12.016-19.853 23.51-19.853h224.65c11.494 0 18.286 8.359 18.286 19.853v333.84zm62.693-61.649-26.122-24.033c-4.18-4.18-5.224-5.224-15.673-3.657v-244.51c1.157-21.321-15.19-39.542-36.51-40.699-0.89-0.048-1.782-0.066-2.673-0.052h-185.47v-20.376c0-11.494 12.016-21.42 23.51-21.42h224.65c11.494 0 18.286 9.927 18.286 21.42v333.32z" />
                                    <path
                                        d="m232.21 104.49h-57.47c-11.542 0-20.898 9.356-20.898 20.898v104.49c0 11.542 9.356 20.898 20.898 20.898h57.469c11.542 0 20.898-9.356 20.898-20.898v-104.49c1e-3 -11.542-9.356-20.898-20.897-20.898zm0 123.3h-57.47v-13.584h57.469v13.584zm0-34.482h-57.47v-67.918h57.469v67.918z" />
                                </svg>
                            </div>
                            <div class="project-content">
                                <h6>Total Active Users</h6>
                                <ul>
                                    <li>
                                        <strong>{{ $users }}</strong>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card overflow-hidden project-card">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="my-auto">
                                <svg enable-background="new 0 0 469.682 469.682" version="1.1"
                                    class="mr-4 ht-60 wd-60 my-auto primary" viewBox="0 0 469.68 469.68"
                                    xml:space="preserve" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="m120.41 298.32h87.771c5.771 0 10.449-4.678 10.449-10.449s-4.678-10.449-10.449-10.449h-87.771c-5.771 0-10.449 4.678-10.449 10.449s4.678 10.449 10.449 10.449z" />
                                    <path
                                        d="m291.77 319.22h-171.36c-5.771 0-10.449 4.678-10.449 10.449s4.678 10.449 10.449 10.449h171.36c5.771 0 10.449-4.678 10.449-10.449s-4.678-10.449-10.449-10.449z" />
                                    <path
                                        d="m291.77 361.01h-171.36c-5.771 0-10.449 4.678-10.449 10.449s4.678 10.449 10.449 10.449h171.36c5.771 0 10.449-4.678 10.449-10.449s-4.678-10.449-10.449-10.449z" />
                                    <path
                                        d="m420.29 387.14v-344.82c0-22.987-16.196-42.318-39.183-42.318h-224.65c-22.988 0-44.408 19.331-44.408 42.318v20.376h-18.286c-22.988 0-44.408 17.763-44.408 40.751v345.34c0.68 6.37 4.644 11.919 10.449 14.629 6.009 2.654 13.026 1.416 17.763-3.135l31.869-28.735 38.139 33.959c2.845 2.639 6.569 4.128 10.449 4.18 3.861-0.144 7.554-1.621 10.449-4.18l37.616-33.959 37.616 33.959c5.95 5.322 14.948 5.322 20.898 0l38.139-33.959 31.347 28.735c3.795 4.671 10.374 5.987 15.673 3.135 5.191-2.98 8.232-8.656 7.837-14.629v-74.188l6.269-4.702 31.869 28.735c2.947 2.811 6.901 4.318 10.971 4.18 1.806 0.163 3.62-0.2 5.224-1.045 5.493-2.735 8.793-8.511 8.361-14.629zm-83.591 50.155-24.555-24.033c-5.533-5.656-14.56-5.887-20.376-0.522l-38.139 33.959-37.094-33.959c-6.108-4.89-14.79-4.89-20.898 0l-37.616 33.959-38.139-33.959c-6.589-5.4-16.134-5.178-22.465 0.522l-27.167 24.033v-333.84c0-11.494 12.016-19.853 23.51-19.853h224.65c11.494 0 18.286 8.359 18.286 19.853v333.84zm62.693-61.649-26.122-24.033c-4.18-4.18-5.224-5.224-15.673-3.657v-244.51c1.157-21.321-15.19-39.542-36.51-40.699-0.89-0.048-1.782-0.066-2.673-0.052h-185.47v-20.376c0-11.494 12.016-21.42 23.51-21.42h224.65c11.494 0 18.286 9.927 18.286 21.42v333.32z" />
                                    <path
                                        d="m232.21 104.49h-57.47c-11.542 0-20.898 9.356-20.898 20.898v104.49c0 11.542 9.356 20.898 20.898 20.898h57.469c11.542 0 20.898-9.356 20.898-20.898v-104.49c1e-3 -11.542-9.356-20.898-20.897-20.898zm0 123.3h-57.47v-13.584h57.469v13.584zm0-34.482h-57.47v-67.918h57.469v67.918z" />
                                </svg>
                            </div>
                            <div class="project-content">
                                <h6>This Month New Users</h6>
                                <ul>
                                    <li>
                                        <strong>{{ $userCurrentMonthCount }}</strong>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card overflow-hidden project-card">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="my-auto">
                                <svg enable-background="new 0 0 469.682 469.682" version="1.1"
                                    class="mr-4 ht-60 wd-60 my-auto primary" viewBox="0 0 469.68 469.68"
                                    xml:space="preserve" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="m120.41 298.32h87.771c5.771 0 10.449-4.678 10.449-10.449s-4.678-10.449-10.449-10.449h-87.771c-5.771 0-10.449 4.678-10.449 10.449s4.678 10.449 10.449 10.449z" />
                                    <path
                                        d="m291.77 319.22h-171.36c-5.771 0-10.449 4.678-10.449 10.449s4.678 10.449 10.449 10.449h171.36c5.771 0 10.449-4.678 10.449-10.449s-4.678-10.449-10.449-10.449z" />
                                    <path
                                        d="m291.77 361.01h-171.36c-5.771 0-10.449 4.678-10.449 10.449s4.678 10.449 10.449 10.449h171.36c5.771 0 10.449-4.678 10.449-10.449s-4.678-10.449-10.449-10.449z" />
                                    <path
                                        d="m420.29 387.14v-344.82c0-22.987-16.196-42.318-39.183-42.318h-224.65c-22.988 0-44.408 19.331-44.408 42.318v20.376h-18.286c-22.988 0-44.408 17.763-44.408 40.751v345.34c0.68 6.37 4.644 11.919 10.449 14.629 6.009 2.654 13.026 1.416 17.763-3.135l31.869-28.735 38.139 33.959c2.845 2.639 6.569 4.128 10.449 4.18 3.861-0.144 7.554-1.621 10.449-4.18l37.616-33.959 37.616 33.959c5.95 5.322 14.948 5.322 20.898 0l38.139-33.959 31.347 28.735c3.795 4.671 10.374 5.987 15.673 3.135 5.191-2.98 8.232-8.656 7.837-14.629v-74.188l6.269-4.702 31.869 28.735c2.947 2.811 6.901 4.318 10.971 4.18 1.806 0.163 3.62-0.2 5.224-1.045 5.493-2.735 8.793-8.511 8.361-14.629zm-83.591 50.155-24.555-24.033c-5.533-5.656-14.56-5.887-20.376-0.522l-38.139 33.959-37.094-33.959c-6.108-4.89-14.79-4.89-20.898 0l-37.616 33.959-38.139-33.959c-6.589-5.4-16.134-5.178-22.465 0.522l-27.167 24.033v-333.84c0-11.494 12.016-19.853 23.51-19.853h224.65c11.494 0 18.286 8.359 18.286 19.853v333.84zm62.693-61.649-26.122-24.033c-4.18-4.18-5.224-5.224-15.673-3.657v-244.51c1.157-21.321-15.19-39.542-36.51-40.699-0.89-0.048-1.782-0.066-2.673-0.052h-185.47v-20.376c0-11.494 12.016-21.42 23.51-21.42h224.65c11.494 0 18.286 9.927 18.286 21.42v333.32z" />
                                    <path
                                        d="m232.21 104.49h-57.47c-11.542 0-20.898 9.356-20.898 20.898v104.49c0 11.542 9.356 20.898 20.898 20.898h57.469c11.542 0 20.898-9.356 20.898-20.898v-104.49c1e-3 -11.542-9.356-20.898-20.897-20.898zm0 123.3h-57.47v-13.584h57.469v13.584zm0-34.482h-57.47v-67.918h57.469v67.918z" />
                                </svg>
                            </div>
                            <div class="project-content">
                                <h6>Total Missions</h6>
                                <ul>
                                    <li>
                                        <strong>{{ $totalMission }}</strong>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- row -->
        <div class="row row-sm ">
            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8">
                <div class="card overflow-hidden">
                    <div class="card-body pb-3">
                        <div class="d-flex justify-content-between">
                            <h4 class="card-title mg-b-10"></h4>
                            <i class="mdi mdi-dots-horizontal text-gray"></i>
                        </div>
                        <div class="table-responsive mb-0 projects-stat tx-14">
                            <div id="users-analytics-chart" class="apex-charts" dir="ltr"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <div class="card overflow-hidden">
                    <div class="card-body pb-3">
                        <div class="d-flex justify-content-between">
                            <h4 class="card-title mg-b-10">Latest 20 Submitted mission</h4>
                            <i class="mdi mdi-dots-horizontal text-gray"></i>
                            <div class="d-flex">
                                <a href="{{ route('admin.la.missions.submissions') }}"><button
                                        class="btn btn-success mb-2 mr-2">View
                                        All</button></a>
                                <form method="get" action="{{ route('home') }}">
                                    <input type="hidden" name="type" value="export">
                                    <button type="submit" data-toggle="tooltip" data-placement="top" title="export"
                                        class="btn btn-purple">Export</button>
                                </form>
                            </div>
                        </div>
                        <div class="table-responsive mb-0 projects-stat tx-14">
                            <table
                                class="table table-hover table-bordered mb-0 text-md-nowrap text-lg-nowrap text-xl-nowrap  ">
                                <thead>
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Image</th>
                                        <th>School Name</th>
                                        <th>District Name</th>
                                        <th>Block Name</th>
                                        <th>Cluster Name</th>
                                        <th>Activity Name</th>
                                        <th>Submission Date</th>
                                        <th>State</th>
                                        <th>City</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (count($latestSubmittedMissions) > 0)
                                        @foreach ($latestSubmittedMissions as $latestSubmittedMission)
                                            <tr>
                                                <td>{{ $latestSubmittedMission->user ? $latestSubmittedMission->user->name : '' }}
                                                </td>
                                                <td>
                                                    @php
                                                        $userImage = $latestSubmittedMission->user ? ($latestSubmittedMission->user->image_path ? $imageBaseUrl . $latestSubmittedMission->user->image_path : '') : '';
                                                    @endphp
                                                    @if ($userImage)
                                                        <a class="image-popup-no-margins" href="{{ $userImage }}">
                                                            <img alt="" src="{{ $userImage }}"
                                                                class="custom-thumbnail">
                                                        </a>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>{{ $latestSubmittedMission->user ? ($latestSubmittedMission->user->school ? $latestSubmittedMission->user->school->name : '') : '' }}
                                                </td>

                                                <td>{{ $latestSubmittedMission->user ? ($latestSubmittedMission->user->school ? $latestSubmittedMission->user->school->district : '') : '' }}
                                                </td>
                                                <td>{{ $latestSubmittedMission->user ? ($latestSubmittedMission->user->school ? $latestSubmittedMission->user->school->block : '') : '' }}
                                                </td>
                                                <td>{{ $latestSubmittedMission->user ? ($latestSubmittedMission->user->school ? $latestSubmittedMission->user->school->cluster : '') : '' }}
                                                </td>

                                                <td>{{ $latestSubmittedMission->laMission ? (isset($latestSubmittedMission->laMission->default_title) ? $latestSubmittedMission->laMission->default_title : '') : '' }}
                                                </td>
                                                <td>{{ date('d-m-Y', strtotime($latestSubmittedMission->created_at)) }}
                                                </td>
                                                <td>{{ $latestSubmittedMission->user ? $latestSubmittedMission->user->state : '' }}
                                                </td>
                                                <td>{{ $latestSubmittedMission->user ? $latestSubmittedMission->user->city : '' }}
                                                </td>
                                                <td><a
                                                        href="{{ route('admin.users.la.missions', $latestSubmittedMission->user_id) }}"><button
                                                            class="btn btn-success">View</button></a></td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <td colspan="4">No Mission Found</td>
                                    @endif
                                </tbody>
                            </table>
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
    <script src="{{ asset('assets/js/apexcharts.min.js') }}"></script>
    <script>
        options = {
            chart: {
                height: 339,
                type: "area",
                stacked: !1,
                toolbar: {
                    show: !1
                }
            },
            stroke: {
                curve: "smooth"
            },
            plotOptions: {
                bar: {
                    columnWidth: "30%"
                }
            },
            colors: ["#010050", "#dfe2e6", "#f1b44c"],
            series: [{
                name: "Users",
                type: "area",
                data: {!! json_encode($yAxis) !!}
            }],
            labels: {!! json_encode($xAxis) !!},
            markers: {
                size: 0
            },
            xaxis: {
                type: "datetime"
            },
            yaxis: {
                title: {
                    text: "Users"
                }
            },
            grid: {
                borderColor: "#f1f1f1"
            }
        };
        (chart = new ApexCharts(document.querySelector("#users-analytics-chart"), options)).render();
    </script>
@endsection
