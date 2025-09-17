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
                    <li class="breadcrumb-item active" aria-current="page">Teachers</li>
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
                    <div class="col-md-12">
                        <h4 class="card-title mg-b-0 mt-2 mb-4">Teacher List ({{ $teachers->total() }})</h4>
                    </div>
                    <form method="get" action="{{ url()->current() }}">
                        <div class=" d-flex row mb-3">
                            <div class="col-md-3 mb-3">
                                <select name="state" id="state" class="form-control">
                                    <option value="">Select State</option>
                                    @if (count($states) > 0)
                                        @foreach ($states as $stateData)
                                            <option value="{{ $stateData->state_name }}"
                                                @if ($stateData->state_name == $request->state) selected @endif>
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
                                                @if ($cityData->city_name == $request->city) selected @endif>
                                                {{ $cityData->city_name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <input class="form-control fc-datepicker" placeholder="Enter School Code" type="text"
                                    value="{{ $request->school_code }}" name="school_code" id="school_code">
                            </div>

                            <div class="col-md-3 mb-3">
                                <select name="is_life_lab" id="is_life_lab" class="form-control">
                                    <option value="">Life Lab User</option>
                                    <option value="1" @if (isset($request->is_life_lab) && $request->is_life_lab == 1) selected @endif>Yes</option>
                                    <option value="0" @if (isset($request->is_life_lab) && $request->is_life_lab == 0) selected @endif>No</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-success">Search</button>
                                <a href={{ url()->current() }} class="btn btn-warning">clear</a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body">
                    <div class="mb-5">
                        <form method="get" action="">
                            <div class="d-flex">
                                <div class="col-md-10"></div>
                                <div class="col-md-2">
                                    <a class="form-control btn btn-success" href="{{ route('admin.teachers.create') }}">Add
                                        Teacher</a>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table id="example" class="table key-buttons text-md-nowrap data-table">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0" scope="col">Sr No.</th>
                                    <th class="border-bottom-0" scope="col">Name</th>
                                    <th class="border-bottom-0" scope="col">Email</th>
                                    <th class="border-bottom-0" scope="col">Mobile Number</th>
                                    <th class="border-bottom-0" scope="col">State</th>
                                    <th class="border-bottom-0" scope="col">City</th>
                                    <th class="border-bottom-0" scope="col">School Code</th>
                                    <th class="border-bottom-0" scope="col">Is Life Lab</th>
                                    <th class="border-bottom-0" scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = $teachers->perPage() * ($teachers->currentPage() - 1) + 1; ?>
                                @foreach ($teachers as $teacher)
                                    <tr>
                                        <td> {{ $i }} </td>
                                        <td> {{ $teacher->name }} </td>
                                        <td> {{ $teacher->email }} </td>
                                        <td> {{ $teacher->mobile_no }} </td>
                                        <td> {{ $teacher->state ?? '' }} </td>
                                        <td> {{ $teacher->city ?? '' }} </td>
                                        <td> {{ $teacher->school->code ?? '' }} </td>
                                        <td>
                                            @if (isset($teacher->school) && $teacher->school->is_life_lab == 1)
                                                <span style="color:green">YES</span>
                                            @else
                                                <span style="color:red">NO</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <a href="{{ route('admin.teachers.edit', $teacher->id) }}"><button
                                                        class='btn btn-purple btn-sm'> Edit </button></a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php $i++; ?>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $teachers->links() }}
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
