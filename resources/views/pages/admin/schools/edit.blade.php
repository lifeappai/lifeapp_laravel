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
                <h4 class="content-title mb-2">{{ __('Schools') }}</h4>
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
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.schools.update', $school->id) }}" id="schoolForm"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row mb-3">
                            <div class="col-md-12 mg-b-20">
                                <h6>Name<span class="text-danger">*</span></h6>
                                <input type="text" name="name" class="form-control" required
                                    value="{{ $school->name }}">
                            </div>
                            <div class="col-md-6 mg-b-20">
                                <h6>State<span class="text-danger">*</span></h6>
                                <select name="state" id="state" class="form-control" required
                                    onchange="setCities(this);">
                                    <option value="">Select State</option>
                                    @foreach ($states as $key => $state)
                                        @php $state = (array)$state; @endphp
                                        @if (isset($state['state_name']))
                                            <option @if ($state['state_name'] == $school->state) selected @endif
                                                value="{{ $state['state_name'] }}">{{ $state['state_name'] }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mg-b-20">
                                <h6>City<span class="text-danger">*</span></h6>
                                <select name="city" id="city" class="form-control" required>
                                    <option value="">Select City</option>
                                    @foreach ($cities as $key => $city)
                                        @php $city = (array)$city; @endphp
                                        @if (isset($city['city_name']))
                                            <option @if ($city['city_name'] == $school->city) selected @endif
                                                value="{{ $city['city_name'] }}">{{ $city['city_name'] }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6  mg-b-20">
                                <h6>Is Life Lab<span class="text-danger">*</span></h6>
                                <select name="is_life_lab" id="is_life_lab" class="form-control" required>
                                    <option value="1" @if($school->is_life_lab == 1) selected @endif>Yes</option>
                                    <option value="0" @if($school->is_life_lab == 0) selected @endif>No</option>
                                </select>
                            </div>

                            <div class="col-md-6  mg-b-20">
                                <h6>App Visible<span class="text-danger">*</span></h6>
                                <select name="app_visible" id="app_visible" class="form-control" required>
                                    <option value="1" @if($school->app_visible == 1) selected @endif>Yes</option>
                                    <option value="0" @if($school->app_visible == 0) selected @endif>No</option>
                                </select>
                            </div>

                            <div class="col-md-6  mg-b-20">
                                <h6>Code</h6>
                                <input name="code" id="code" class="form-control"onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                    @if ($school->code)
                                value="{{ $school->code }}"
                                @endif>
                            </div>

                            <div class="col-md-12">
                                <button type="submit" class="btn btn-success">Submit</button>
                            </div>
                        </div>
                        <br>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('assets/js/parsley.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("#schoolForm").parsley()
        });

        function setCities(sel) {
            var url = '{{ route('admin.cities', ':state') }}';
            url = url.replace(':state', sel.value);

            const xhttp = new XMLHttpRequest();
            xhttp.open("GET", url, false);
            xhttp.send();
            const res = xhttp.responseText

            const cities = JSON.parse(res)
            var options = `<option selected value="">Select a city</option>`
            for (let i = 0; i < cities.length; i++) {
                const city = cities[i];
                options = options + `<option value="${city["city_name"]}">${city["city_name"]}</option>`
            }
            $('#city').html(options)
        }
    </script>
@endsection
