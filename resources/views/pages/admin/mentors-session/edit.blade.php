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
                <h4 class="content-title mb-2">{{ __('Session') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Session</li>
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
                    <form method="POST" action="{{ route('admin.la.sessions.update', ['laSession' => $laSession->id]) }}"
                        id="MentorSessionForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row mb-3">
                            <div class="col-md-12 mg-b-20">
                                <h6>Mentor Name<span class="text-danger">*</span></h6>
                                <input type="text" name="name" class="form-control" required
                                    value="{{ $laSession->user->name }}" disabled>
                            </div>
                            <div class="col-md-6  mg-b-20">
                                <h6>Status<span class="text-danger">*</span></h6>
                                <select name="status" id="status" class="form-control" required>
                                    <option value="{{ App\Enums\StatusEnum::ACTIVE }}"
                                        @if ($laSession->status == App\Enums\StatusEnum::ACTIVE) selected @endif>Active</option>
                                    <option value="{{ App\Enums\StatusEnum::DEACTIVE }}"
                                        @if ($laSession->status == App\Enums\StatusEnum::DEACTIVE) selected @endif>Deactive</option>
                                </select>
                            </div>
                            <div class="col-md-6  mg-b-20">
                                <h6>Heading<span class="text-danger">*</span></h6>
                                <input name="heading" id="heading" class="form-control" required
                                    @if ($laSession->heading) value="{{ $laSession->heading }}" @endif>
                            </div>
                            <div class="col-md-12  mg-b-20">
                                <h6>Description<span class="text-danger">*</span></h6>
                                <textarea name="description" id="description" rows="10" required class="form-control">@if ($laSession->description) {{ $laSession->description }} @endif</textarea>
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
            $("#MentorSessionForm").parsley()
        });
    </script>
@endsection
