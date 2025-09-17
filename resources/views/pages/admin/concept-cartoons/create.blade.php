@extends('layouts.admin')
@section('css')
    <style>
        .error {
            color: red;
            margin-top: 5px;
        }

        .nav-tabs .nav-link.active {
            border-bottom: 5px solid #ff9501 !important;
        }
    </style>
@endsection
@section('breadcrumb')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div>
            <h4 class="content-title mb-2"></h4>
            <nav aria-label="breadcrumb">
                <h4 class="content-title mb-2">{{ __('Concept Cartoons') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Concept Cartoons</li>
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
                    <form method="POST" action="{{ route('admin.concept.cartoons.store') }}" id="competencyForm"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-3 mt-3">
                            <div class="col-md-6 mg-b-20">
                                <h6>Select Subject<span class="text-danger">*</span></h6>
                                <select name="la_subject_id" class="form-control" required>
                                    <option value="">Select Subject</option>
                                    @foreach ($subjects as $key => $subject)
                                        <option value="{{ $subject->id }}">{{ $subject->default_title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mg-b-20">
                                <h6>Select Level<span class="text-danger">*</span></h6>
                                <select name="la_level_id" class="form-control" required>
                                    <option value="">Select Level</option>
                                    @foreach ($laLevels as $key => $laLevel)
                                        <option value="{{ $laLevel->id }}">{{ $laLevel->default_title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12  mg-b-20">
                                <h6>Title<span class="text-danger">*</span></h6>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="col-md-12  mg-b-20">
                                <h6>Select Document<span class="text-danger">*</span></h6>
                                <input type="file" class="form-control" id="document" name="document" required=""
                                    autocomplete="off" onchange="loadFile(this)">
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
    <script src="{{ asset('assets/js/parsley.min.js') }}"></script>
    <script>
        $("#competencyForm").parsley();
        var loadFile = function(event) {
            var filePath = event.value;
            var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.jfif|\.pdf)$/i;
            if (!allowedExtensions.exec(filePath)) {
                alert('Please upload file having extensions .jpeg, .jpg .png only.');
                event.value = '';
            } else {
                if (event.files && event.files[0]) {
                    var reader = new FileReader();
                    reader.readAsDataURL(event.files[0]);
                }
            }
        };
    </script>
@endsection
