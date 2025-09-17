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
                <h4 class="content-title mb-2">{{ __('Schools') }} ({{ $schools->total() }})</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Schools</li>
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
                                    <form method="get" action="{{ route('admin.schools.index') }}">
                                        <div class=" d-flex row mb-3">
                                            <div class="col-md-3 mb-3">
                                                <input type="text" name="school_name" class="form-control"
                                                    @if ($request->school_name) value="{{ $request->school_name }}" @endif
                                                    placeholder="Enter School name">
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <select name="status" class="form-control" id="status">
                                                    <option value="" selected>Select Status</option>
                                                    <option value="{{ App\Enums\StatusEnum::ACTIVE }}"
                                                        @if ($status == App\Enums\StatusEnum::ACTIVE) selected @endif>
                                                        Active</option>
                                                    <option value="{{ App\Enums\StatusEnum::DEACTIVE }}"
                                                        @if ($status != '' && $status == App\Enums\StatusEnum::DEACTIVE) selected @endif>
                                                        Inactive</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <select name="state" id="state" class="form-control">
                                                    <option value="">Select State</option>
                                                    @if (count($states) > 0)
                                                        @foreach ($states as $stateData)
                                                            <option value="{{ $stateData->state_name }}"
                                                                @if ($stateData->state_name == $state) selected @endif>
                                                                {{ $stateData->state_name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <select name="city" id="city" class="form-control">
                                                    <option value="">Select City</option>
                                                    @if (count($cities) > 0)
                                                        @foreach ($cities as $cityData)
                                                            <option value="{{ $cityData->city_name }}"
                                                                @if ($cityData->city_name == $city) selected @endif>
                                                                {{ $cityData->city_name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <input type="text" name="code" class="form-control"
                                                    @if ($code) value="{{ $code }}" @endif
                                                    placeholder="Enter Code">
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <input type="text" name="district" class="form-control"
                                                    @if ($district) value="{{ $district }}" @endif
                                                    placeholder="Enter District">
                                            </div>
                                            <div class="col-md-3">
                                                <button type="submit" class="btn btn-success">Search</button>
                                                <button href={{ route('admin.schools.index') }} class="btn btn-warning"
                                                    onclick="clearFilter()">clear</a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="">
                        <form method="get" action="">
                            <div class="d-flex">
                                <div class="col-md-10"></div>
                                <div class="col-md-2">
                                    <a class="form-control btn btn-success" href="{{ route('admin.schools.create') }}">Add
                                        School</a>
                                </div>
                            </div>
                        </form>
                    </div>
                    <form method="POST" action="{{ route('admin.schools.import') }}" id="languageForm"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="col-md-6  mg-b-20">
                            <h6>Import excel sheet<span class="text-danger">*</span></h6>
                            <input type="file" class="form-control mb-2" name="school_excel_sheet"
                                id="school_excel_sheet">
                            <label>Download Sample File From Here: <a
                                    href="{{ asset('assets/excel/School_Import.xlsx') }}">
                                    <b>School Import File</b></a></label>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-success">Submit</button>
                        </div>
                        <br>
                    </form>
                    <div class="table-responsive">
                        <table id="example" class="table key-buttons text-md-nowrap data-table">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0" scope="col">Sr No.</th>
                                    <th class="border-bottom-0" scope="col">Name</th>
                                    <th class="border-bottom-0" scope="col">State</th>
                                    <th class="border-bottom-0" scope="col">City</th>
                                    <th class="border-bottom-0" scope="col">District</th>
                                    <th class="border-bottom-0" scope="col">App Visible</th>
                                    <th class="border-bottom-0" scope="col">Is Life Lab</th>
                                    <th class="border-bottom-0" scope="col">Code</th>
                                    <th class="border-bottom-0" scope="col">Status</th>
                                    <th class="border-bottom-0" scope="col">Action</th>
                                </tr>
                            </thead>
                            <?php $i = $schools->perPage() * ($schools->currentPage() - 1) + 1; ?>
                            <tbody>
                                @if (count($schools) > 0)
                                    @foreach ($schools as $school)
                                        <tr class="user_list">
                                            <td>{{ $i }}</td>
                                            <td>{{ $school->name }}</td>
                                            <td>{{ $school->state }}</td>
                                            <td>{{ $school->city }}</td>
                                            <td>{{ $school->district ?? '' }}</td>
                                            <td>
                                                @if ($school->is_life_lab == 1)
                                                    <span style="color:green">YES</span>
                                                @else
                                                    <span style="color:red">NO</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($school->app_visible == 1)
                                                    <span style="color:green">YES</span>
                                                @else
                                                    <span style="color:red">NO</span>
                                                @endif
                                            </td>
                                            <td>{{ $school->code ?? '' }}</td>
                                            <td>
                                                @if ($school->status == App\Enums\StatusEnum::ACTIVE)
                                                    <button type="button"
                                                        class="btn btn-sm btn-success waves-effect waves-light"
                                                        onclick="sweetAlertAjax('PATCH','{{ route('admin.schools.status', $school->id) }}', 'Are You Sure To Inactive')">Active</button>
                                                @else
                                                    <button type="button"
                                                        class="btn btn-sm btn-danger waves-effect waves-light"
                                                        onclick="sweetAlertAjax('PATCH','{{ route('admin.schools.status', $school->id) }}', 'Are You Sure To Active')">Inactive</button>
                                                @endif
                                            </td>
                                            <td>
                                                <a class="mr-2" href="{{ route('admin.schools.edit', $school->id) }}">
                                                    <button type="button" data-toggle="tooltip" data-placement="top"
                                                        title="edit" class="btn btn-purple btn-sm">
                                                        Edit
                                                    </button>
                                                </a>

                                                <button type="button"
                                                    class="btn btn-sm btn-danger waves-effect waves-light"
                                                    onclick="sweetAlertAjax('DELETE', '{{ route('admin.schools.destroy', $school->id) }}', 'Are You Sure To Delete')">Delete</button>
                                            </td>

                                        </tr>
                                        <?php $i++; ?>
                                    @endforeach
                                @else
                                    <td class="text-center" colspan="7">No schools Found </td>
                                @endif
                            </tbody>
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
    <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
    <script>
        function clearFilter() {
            $('#status').val('').trigger('change');
            $('#state').val('').trigger('change');
            $('#city').val('').trigger('change');
        }

        $("#state").change(function() {
            var state = $(this).val();
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
                },
                success: function(data) {
                    $("#city").html(data);
                }
            });
        });
    </script>
@endsection
