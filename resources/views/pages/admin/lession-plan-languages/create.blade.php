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
                <h4 class="content-title mb-2">{{ __('Lession Plan Languages') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Lession Plan Languages</li>
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
                    <form method="POST" action="{{ route('admin.lession.plan.languages.store') }}" id="languageForm"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6  mg-b-20">
                                <h6>Name<span class="text-danger">*</span></h6>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="Enter Language" required="" autocomplete="off">
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-success">Submit</button>
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
    <script src="{{ asset('assets/js/jquery.repeater.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("#languageForm").parsley();
        });
    </script>
@endsection
