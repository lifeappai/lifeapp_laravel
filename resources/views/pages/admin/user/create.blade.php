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
                <h4 class="content-title mb-2">{{ __('Users') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Users</li>
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
                    <form method="POST" action="{{ route('admin.users.create') }}" id="studentForm"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6 mg-b-20">
                                <h6>Name<span class="text-danger">*</span></h6>
                                <input type="text" name="name" class="form-control" required
                                    onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) ||  (event.charCode = 32)">
                            </div>
                            <div class="col-md-6 mg-b-20">
                                <h6>Email</h6>
                                <input type="email" name="email" class="form-control">
                            </div>
                            <div class="col-md-6 mg-b-20">
                                <h6>Mobile Number<span class="text-danger">*</span></h6>
                                <input type="text" name="mobile_no" class="form-control" required maxlength="10"
                                    onkeypress='return event.charCode >= 48 && event.charCode <= 57' minlength="10">
                            </div>
                            <div class="col-md-6 mg-b-20">
                                <h6>Grade<span class="text-danger">*</span></h6>
                                <input type="text" name="grade" id="grade" class="form-control" required
                                    onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
                            </div>
                            <div class="parsley-input col-md-6  mg-b-20">
                                <label>{{ __('State') }}</label>
                                <select name="state" id="state" class="form-control">
                                    <option value="">Select State</option>
                                    @if (count($states) > 0)
                                        @foreach ($states as $state)
                                            <option value="{{ $state->state_name }}">{{ $state->state_name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="parsley-input col-md-6  mg-b-20">
                                <label>{{ __('City') }}</label>
                                <select name="city" id="city" class="form-control">
                                    <option value="">Select City</option>
                                </select>
                            </div>

                            <div class="col-md-6 mg-b-20">
                                <h6>School</h6>
                                <select name="school_id" id="school_id" class="form-control">
                                    <option value="">Select School</option>
                                    @foreach ($schools as $school)
                                        <option value="{{ $school->id }}">{{ $school->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="parsley-input col-md-12 mg-b-20">
                                <label>{{ __('Address') }}<span class="tx-danger">*</span></label>
                                <textarea class="form-control" name="address" id="address" cols="30" rows="5"></textarea>
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
            $("#studentForm").parsley()
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
