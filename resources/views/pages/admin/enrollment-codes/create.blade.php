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
                <h4 class="content-title mb-2">{{ __('Game Enrollments') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Game Enrollments</li>
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
                    <form method="POST" action="{{ route('admin.game.enrollments.store') }}" id="enrollmentForm"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6  mg-b-20">
                                <h6>Type<span class="text-danger">*</span></h6>
                                @foreach (App\Enums\GameEnrollmentTypeEnum::TYPE as $key => $type)
                                    <option value="{{ $type }}" @if ($type == $type) selected @endif>
                                        {{ ucwords(str_replace('_', ' ', strtolower($key))) }}
                                    </option>
                                @endforeach
                            </div>
                            <div class="col-md-6  mg-b-20">
                                <h6>Enrollment Code Count<span class="text-danger">*</span></h6>
                                <input type="text" name="enrollment_code_count" id="enrollment_code_count"
                                    class="form-control" required
                                    onkeypress='return event.charCode >= 48 && event.charCode <= 57' required>
                                <label id="enrollment_code_count_error" style="color:red">
                            </div>
                            <div class="col-md-12">
                                <button type="submit" id="submit" class="btn btn-success">Submit</button>
                            </div>
                        </div>
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
            $("#enrollmentForm").parsley();
        });
    </script>
@endsection
