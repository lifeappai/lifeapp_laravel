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
                <h4 class="content-title mb-2">{{ __('Mentors') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Mentors</li>
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
                    <form method="POST" action="{{ route('admin.mentors.update', $user->id) }}" id="mentorForm"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row mb-3">
                            <div class="col-md-6 mg-b-20">
                                <h6>Name<span class="text-danger">*</span></h6>
                                <input type="text" name="name" class="form-control" required
                                    value="{{ $user->name }}"
                                    onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) ||  (event.charCode = 32)">
                            </div>
                            <div class="col-md-6 mg-b-20">
                                <h6>Email<span class="text-danger">*</span></h6>
                                <input type="email" name="email" class="form-control" required
                                    value="{{ $user->email }}">
                            </div>
                            <div class="col-md-6 mg-b-20">
                                <h6>Mobile Number<span class="text-danger">*</span></h6>
                                <input type="text" name="mobile_no" class="form-control" required maxlength="10"
                                    onkeypress='return event.charCode >= 48 && event.charCode <= 57' minlength="10"
                                    value="{{ $user->mobile_no }}">
                            </div>
                            <div class="col-md-4 mg-b-20">
                                <h6>Mentor Code<span class="text-danger">*</span></h6>
                                <input type="text" name="pin" id="pin" minlength="4" maxlength="4"
                                    class="form-control" required
                                    onkeypress='return event.charCode >= 48 && event.charCode <= 57'
                                    value="{{ $user->pin }}">
                            </div>
                            <div class="col-md-2 mg-b-20">
                                <h6 style="opacity: 0"> a</h6>
                                <button class="btn btn-warning" type="button" onclick="generateCode()"> Generate
                                    Code</button>
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
            $("#mentorForm").parsley()
        });

        function generateCode() {
            var code = Math.floor(1000 + Math.random() * 9000);
            $("#pin").val(code);
        }
    </script>
@endsection
