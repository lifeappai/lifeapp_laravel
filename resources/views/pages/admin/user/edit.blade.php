@extends('layouts.admin')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}">
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
                <h4 class="content-title mb-2">{{ __('Users') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.list') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $user->name }}</li>
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
                    <form method="POST" action="{{ route('admin.users.edit', $user->id) }}" id="chapterForm"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="parsley-input col-md-6  mg-b-20">
                                <label>{{ __('Name') }}<span class="tx-danger">*</span></label>
                                <input type="text" value="{{ $user->name }}" name="name" id="name"
                                    onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) ||  (event.charCode = 32)"
                                    class="form-control">
                            </div>
                            <div class="parsley-input col-md-6  mg-b-20">
                                <label>{{ __('Mobile Number') }}<span class="tx-danger">*</span></label>
                                <input type="text" value="{{ $user->mobile_no }}" name="mobile_no" id="mobile_no"
                                    class="form-control" maxlength="10"
                                    onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
                            </div>
                            <div class="parsley-input col-md-6  mg-b-20">
                                <label>{{ __('School Name') }}</label>
                                <select name="school_id" id="school_id" class="form-control">
                                    <option value="">Select School</option>
                                    @if (count($schools) > 0)
                                        @foreach ($schools as $school)
                                            <option value="{{ $school->id }}"
                                                @if ($school->id == $user->school_id) selected @endif>{{ $school->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="parsley-input col-md-6  mg-b-20">
                                <label>{{ __('State') }}</label>
                                <select name="state" id="state" class="form-control">
                                    <option value="">Select State</option>
                                    @if (count($states) > 0)
                                        @foreach ($states as $state)
                                            <option value="{{ $state->state_name }}"
                                                @if ($state->state_name == $user->state) selected @endif>{{ $state->state_name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="parsley-input col-md-6  mg-b-20">
                                <label>{{ __('City') }}</label>
                                <select name="city" id="city" class="form-control">
                                    <option value="">Select City</option>
                                    @if (count($cities) > 0)
                                        @foreach ($cities as $city)
                                            <option value="{{ $city->city_name }}"
                                                @if ($city->city_name == $user->city) selected @endif>{{ $city->city_name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="parsley-input col-md-6  mg-b-20">
                                <label>{{ __('Grade') }}<span class="tx-danger">*</span></label>
                                <input type="text" value="{{ $user->grade }}" name="grade" id="grade"
                                    class="form-control" maxlength="5"
                                    onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
                            </div>
                            <div class="parsley-input col-md-12 mg-b-20">
                                <label>{{ __('Address') }}<span class="tx-danger">*</span></label>
                                <textarea class="form-control" name="address" id="address" cols="30" rows="5">{{ $user->address }}</textarea>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-lg-12 col-md-6">
                                <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                            </div>
                        </div>
                        <br>
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
        $(document).ready(function() {
            $('#chapterForm').validate({
                ignore: [],
                rules: {
                    name: {
                        required: true
                    },
                    mobile_no: {
                        required: true
                    },
                    school_id: {
                        required: true
                    },
                    grade: {
                        required: true,
                    },
                },
                messages: {
                    name: {
                        required: "Please Enter Name"
                    },
                    mobile_no: {
                        required: "Please Enter Mobile Number"
                    },
                    school_id: {
                        required: "Please Enter School Name"
                    },
                    grade: {
                        required: "Please Enter Grade"
                    },
                }
            });
        });
        $("#state").change(function() {
            var state = $(this).val();
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
