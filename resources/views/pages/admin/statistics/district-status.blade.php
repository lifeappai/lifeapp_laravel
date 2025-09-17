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
                <h4 class="content-title mb-2">{{ __('District Status as of Now') }}</h4>
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
                            <form action="{{ route('admin.chhattisgarh.district.status') }}" method="GET">
                                <input type="hidden" name="type" value="export">
                                <button type="submit" class="form-control btn btn-success">
                                    Generate District Data
                                </button>
                            </form>
                        </div>
                        @if ($filePath != null)
                            <div class="col-md-4">
                                <a href="{{ 'https://media.gappubobo.com/' . $filePath }}" class="btn btn-purple ml-2">Export
                                    District Data</a>
                            </div>
                        @endif
                    </div>
                    <br>
                    <div class="table-responsive">
                        <div class="row">
                            <div class="col-md-8">
                                <table id="example" class="table key-buttons text-md-nowrap table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="border-bottom-0 text-center" colspan="5">Har Ghar Ballon Car -
                                                2024,
                                                Chhattisgarh, as of {{ now()->format('jS F Y') }} ({{ $schools->total() }})
                                            </th>

                                        </tr>
                                        <tr>
                                            <th class="border-bottom-0" scope="col">Sr No</th>
                                            <th class="border-bottom-0" scope="col">District</th>
                                            <th class="border-bottom-0" scope="col">Total no. of Schools</th>
                                            <th class="border-bottom-0" scope="col">Schools Enrolled</th>
                                            <th class="border-bottom-0" scope="col">% Enrolled</th>
                                        </tr>
                                    </thead>
                                    @php
                                        $totalSchools = 0;
                                        $totalEnrolledSchools = 0;
                                        $totalPercentage = $totalSchools != 0 ? ($totalEnrolledSchools / $totalSchools) * 100 : 0;
                                    @endphp
                                    <?php $i = $schools->perPage() * ($schools->currentPage() - 1) + 1; ?>
                                    @foreach ($schools as $school)
                                        @php
                                            $totalSchoolCount = $school->getDistrictCount($school->district, 'Chhattisgarh');
                                            $totalSchools += $totalSchoolCount;
                                            $totalSchoolEnrolledCount = $school->getUserCount($school->district, 'Chhattisgarh', '2024-02-12');
                                            $totalEnrolledSchools += $totalSchoolEnrolledCount;
                                        @endphp
                                        <tbody>
                                            <tr>
                                                <td>{{ $i }}</td>
                                                <th>{{ $school->district }}</th>
                                                <td>{{ $totalSchoolCount ?? 0 }}
                                                </td>
                                                <td>{{ $totalSchoolEnrolledCount ?? 0 }}
                                                </td>
                                                <td>{{ $totalSchoolEnrolledCount != 0 ? number_format(($totalSchoolEnrolledCount / $totalSchoolCount) * 100, 2) . '%' : '-' }}
                                                </td>
                                            </tr>
                                        </tbody>
                                        <?php $i++; ?>
                                    @endforeach
                                    <tr>
                                        <td colspan="2"><strong>Total</strong></td>
                                        <td>{{ $totalSchools }}</td>
                                        <td>{{ $totalEnrolledSchools }}</td>
                                        <td>{{ $totalEnrolledSchools != 0 ? number_format(($totalEnrolledSchools / $totalSchools) * 100, 2) . '%' : '-' }}
                                        </td>
                                    </tr>
                                </table>
                                @if (count($schools) > 0)
                                    {{ $schools->appends(Request::all())->links() }}
                                @endif
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
    <script src="{{ asset('assets/js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
@endsection
