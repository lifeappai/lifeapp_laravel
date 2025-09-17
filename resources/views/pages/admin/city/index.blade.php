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
                <h4 class="content-title mb-2">{{ __('Cities') }} ({{ $cities['state_name'] }})</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Cities</li>
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
                    <div class="mb-5">
                        <form method="get" action="">
                            <div class="d-flex">
                                <div class="col-md-10"></div>
                                <div class="col-md-2">
                                    <a class="form-control btn btn-success" href="#" onclick="addCity()">Add
                                        City</a>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table id="example" class="table key-buttons text-md-nowrap">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0" scope="col">Sr No.</th>
                                    <th class="border-bottom-0" scope="col">Name</th>
                                    <th class="border-bottom-0" scope="col">Status</th>
                                </tr>
                            </thead>
                            <?php $i = 1; ?>
                            <tbody>
                                @php
                                    $i = 1;
                                @endphp
                                @if (isset($cities['cities']))
                                    @foreach ($cities['cities'] as $key1 => $city)
                                        <tr>
                                            <td>{{ $i }}</td>
                                            <td>{{ $city['city_name'] }}</td>
                                            <td>
                                                @if (isset($city['active']) && App\Enums\StatusEnum::DEACTIVE == $city['active'])
                                                    <button type="button"
                                                        class="btn btn-sm btn-danger waves-effect waves-light"
                                                        onclick="sweetAlertAjax('POST','{{ route('admin.update.status', [$key1, 'type' => 'city', 'state_name' => $cities['state_name'], 'city_name' => $city['city_name']]) }}', 'Are You Sure To Active')">Deactive</button>
                                                @else
                                                    <button type="button"
                                                        class="btn btn-sm btn-success waves-effect waves-light"
                                                        onclick="sweetAlertAjax('POST','{{ route('admin.update.status', [$key1, 'type' => 'city', 'state_name' => $cities['state_name'], 'city_name' => $city['city_name']]) }}', 'Are You Sure To Deactive')">Active</button>
                                                @endif
                                            </td>
                                        </tr>
                                        @php $i++; @endphp
                                    @endforeach
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

    <div class="modal fade" id="addCityFormModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add City</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('admin.add.city', $id) }}" id="addCityForm" class="addCityForm">
                        @csrf
                        <br>
                        <h6>State: {{ $cities['state_name'] }} </h6>
                        <br>
                        <h6>City<span class="text-danger">*</span></h6>
                        <input type="text" name="city_name" id="city_name" class="form-control">
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
    <script src="{{ asset('assets/js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("#generateCodeForm").parsley()
        });

        function addCity(subjectId) {
            $("#addCityFormModal").modal('show');
        }

        $('#addCityForm').validate({
            ignore: [],
            rules: {
                state_name: {
                    required: true,
                },
                city_name: {
                    required: true,
                },
            },
            messages: {
                state_name: {
                    required: "Please Enter State Name",
                },
                city_name: {
                    required: "Please Enter City Name",
                },
            }
        });
    </script>
@endsection
