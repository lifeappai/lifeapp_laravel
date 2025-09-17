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
                <h4 class="content-title mb-2">{{ __('States') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">States</li>
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
                                    <a class="form-control btn btn-success" href="#" onclick="addState()">Add
                                        State</a>
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
                                    <th class="border-bottom-0" scope="col">View</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $i = 1;
                                @endphp
                                @if (count($states) > 0)
                                    @foreach ($states as $key => $state)
                                        <tr>
                                            <td>{{ $i }}</td>
                                            <td>{{ $state['state_name'] }}</td>
                                            <td>
                                                @if (isset($state['active']) && App\Enums\StatusEnum::DEACTIVE == $state['active'])
                                                    <button type="button"
                                                        class="btn btn-sm btn-danger waves-effect waves-light"
                                                        onclick="sweetAlertAjax('POST','{{ route('admin.update.status', [$key, 'type' => 'state']) }}', 'Are You Sure To Active')">Deactive</button>
                                                @else
                                                    <button type="button"
                                                        class="btn btn-sm btn-success waves-effect waves-light"
                                                        onclick="sweetAlertAjax('POST','{{ route('admin.update.status', [$key, 'type' => 'state']) }}', 'Are You Sure To Deactive')">Active</button>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.get.cities', $key) }}">View Cities</a>
                                            </td>
                                        </tr>
                                        @php $i++; @endphp
                                    @endforeach
                                @else
                                    <tr>
                                        <td class="text-center" colspan="10">No Data Found </td>
                                    </tr>
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

    <div class="modal fade" id="addStateFormModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add State</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('admin.add.states') }}" id="addStateForm" class="addStateForm">
                        @csrf
                        <br>
                        <h6>Country<span class="text-danger">*</span></h6>
                        <select name="country_name" id="country_name" class="form-control">
                            <option value="India">India</option>
                        </select>
                        <br>
                        <h6>State<span class="text-danger">*</span></h6>
                        <input type="text" name="state_name" id="state_name" class="form-control">
                        <br>
                        <button type="submit" class="btn btn-success">submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('assets/js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
    <script>
        function addState() {
            $("#addStateFormModal").modal('show');
        }

        $('#addStateForm').validate({
            ignore: [],
            rules: {
                state_name: {
                    required: true,
                },
            },
            messages: {
                state_name: {
                    required: "Please Enter State Name",
                },
            }
        });
    </script>
@endsection
