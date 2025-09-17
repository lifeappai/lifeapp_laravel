@extends('layouts.admin')
@section('css')
    <style>
        .error {
            color: red;
            margin-top: 5px;
        }
    </style>
@endsection
@section('breadcrumb')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div>
            <h4 class="content-title mb-2"></h4>
            <nav aria-label="breadcrumb">
                <h4 class="content-title mb-2">{{ __('Status as of ') }} {{ now()->format('jS F Y') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Status</li>
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
                                href="{{ route('admin.chhattisgarh.district.status') }}">Chhattisgarh District Data</a>
                        </div>
                        <div class="col-md-4">
                            <form action="{{ route('admin.chhattisgarh.student.status') }}" method="GET">
                                <input type="hidden" name="type" value="export">
                                <button type="submit" class="form-control btn btn-warning">
                                    Generate Chhattisgarh Student Data
                                </button>
                            </form>
                        </div>
                        @if ($filePath != null)
                            <div class="col-md-4">
                                <a href="{{ 'https://media.gappubobo.com/' . $filePath }}"
                                    class="btn btn-purple ml-2">Export
                                    Student Data</a>
                            </div>
                        @endif
                    </div>
                    <br>
                    <div class="table-responsive">
                        <div class="row">
                            <div class="col-md-7">

                                <table id="example" class="table key-buttons text-md-nowrap table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="border-bottom-0" colspan="2" rowspan="2">Particular</th>
                                            <th class="border-bottom-0 text-center" colspan="3">Downloads</th>
                                        </tr>
                                        <tr>
                                            <th class="border-bottom-0" scope="col">Numbers</th>
                                            <th class="border-bottom-0" scope="col">Target</th>
                                            <th class="border-bottom-0" scope="col">Achieved</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th colspan="2">Total Downloads</th>
                                            <td>{{ $schoolCounts['total_downloads'] ?? '' }}</td>
                                            <td>{{ 48513 }}</td>
                                            <td>{{ $schoolCounts['total_downloads'] != 0 ? number_format(($schoolCounts['total_downloads'] / 48513) * 100, 2) . '%' : '-' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th colspan="2">No of Schools</th>
                                            <td>{{ $schoolCounts['school_count'] }}</td>
                                            <td>{{ 48513 }}</td>
                                            <td>{{ $schoolCounts['school_count'] != 0 ? number_format(($schoolCounts['school_count'] / 48513) * 100, 2) . '%' : '-' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th colspan="2">No of Village/Wards</th>
                                            <td>{{ $schoolCounts['city_count'] }}</td>
                                            <td>{{ 16101 }}</td>
                                            <td>{{ $schoolCounts['city_count'] != 0 ? number_format(($schoolCounts['city_count'] / 16101) * 100, 2) . '%' : '-' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th colspan="2">No of Cluster</th>
                                            <td>{{ $schoolCounts['cluster_count'] }}</td>
                                            <td>{{ 2461 }}</td>
                                            <td>{{ $schoolCounts['cluster_count'] != 0 ? number_format(($schoolCounts['cluster_count'] / 2461) * 100, 2) . '%' : '-' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th colspan="2">No of Block</th>
                                            <td>{{ $schoolCounts['block_count'] }}</td>
                                            <td>{{ 145 }}</td>
                                            <td>{{ $schoolCounts['block_count'] != 0 ? number_format(($schoolCounts['block_count'] / 145) * 100, 2) . '%' : '-' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th colspan="2">Total of District</th>
                                            <td>{{ $schoolCounts['district_count'] }}</td>
                                            <td>{{ 28 }}</td>
                                            <td>{{ $schoolCounts['district_count'] != 0 ? number_format(($schoolCounts['district_count'] / 28) * 100, 2) . '%' : '-' }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-5">
                                <table id="example" class="table key-buttons text-md-nowrap table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="border-bottom-0 text-center" colspan="3">Baloon Car Mission</th>
                                        </tr>
                                        <tr>
                                            <th class="border-bottom-0" scope="col">Numbers</th>
                                            <th class="border-bottom-0" scope="col">Target</th>
                                            <th class="border-bottom-0" scope="col">Achieved</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ $missionSchoolCounts['total_downloads'] }}</td>
                                            <td>{{ 48513 }}</td>
                                            <td>{{ $missionSchoolCounts['total_downloads'] != 0 ? number_format(($missionSchoolCounts['total_downloads'] / 48513) * 100, 2) . '%' : '-' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{ $missionSchoolCounts['school_count'] }}</td>
                                            <td>{{ 48513 }}</td>
                                            <td>{{ $missionSchoolCounts['school_count'] != 0 ? number_format(($missionSchoolCounts['school_count'] / 48513) * 100, 2) . '%' : '-' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{ $missionSchoolCounts['city_count'] }}</td>
                                            <td>{{ 16101 }}</td>
                                            <td>{{ $missionSchoolCounts['city_count'] != 0 ? number_format(($missionSchoolCounts['city_count'] / 16101) * 100, 2) . '%' : '-' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{ $missionSchoolCounts['cluster_count'] }}</td>
                                            <td>{{ 2461 }}</td>
                                            <td>{{ $missionSchoolCounts['cluster_count'] != 0 ? number_format(($missionSchoolCounts['cluster_count'] / 2461) * 100, 2) . '%' : '-' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{ $missionSchoolCounts['block_count'] }}</td>
                                            <td>{{ 145 }}</td>
                                            <td>{{ $missionSchoolCounts['block_count'] != 0 ? number_format(($missionSchoolCounts['block_count'] / 145) * 100, 2) . '%' : '-' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{ $missionSchoolCounts['district_count'] }}</td>
                                            <td>{{ 28 }}</td>
                                            <td>{{ $missionSchoolCounts['district_count'] != 0 ? number_format(($missionSchoolCounts['district_count'] / 28) * 100, 2) . '%' : '-' }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('assets/js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
@endsection
