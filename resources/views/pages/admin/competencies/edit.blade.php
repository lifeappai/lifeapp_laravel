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

        .custom-thumbnail {
            width: 30px;
            height: 30px;
            border-radius: 50%;
        }
    </style>
@endsection
@section('breadcrumb')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div>
            <h4 class="content-title mb-2"></h4>
            <nav aria-label="breadcrumb">
                <h4 class="content-title mb-2">{{ __('Competencies') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Competencies</li>
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
                    <form method="POST" action="{{ route('admin.competencies.update', $laCompetency->id) }}"
                        id="missionsForm" enctype="multipart/form-data">
                        @csrf
                        @method('put')
                        <div class="row mb-3 mt-3">
                            <div class="col-md-6  mg-b-20">
                                <h6>Select Subject<span class="text-danger">*</span></h6>
                                <select name="la_subject_id" class="form-control" required>
                                    <option value="">Select Subject</option>
                                    @foreach ($subjects as $key => $subject)
                                        <option value="{{ $subject->id }}"
                                            @if ($laCompetency->la_subject_id == $subject->id) selected @endif>{{ $subject->default_title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6  mg-b-20">
                                <h6>Select Level Type<span class="text-danger">*</span></h6>
                                <select name="la_level_id" class="form-control" required>
                                    <option value="">Select Level Type</option>
                                    @foreach ($laLevels as $key => $laLevel)
                                        <option value="{{ $laLevel->id }}"
                                            @if ($laCompetency->la_level_id == $laLevel->id) selected @endif>{{ $laLevel->default_title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12  mg-b-20">
                                <h6>Title<span class="text-danger">*</span></h6>
                                <input type="text" name="title" class="form-control" required
                                    value="{{ $laCompetency->title }}">
                            </div>
                            <div class="col-md-10  mg-b-20">
                                <h6>Select Document</h6>
                                <input type="file" class="form-control" id="document" name="document"
                                    autocomplete="off"onchange="loadFile(this)">
                            </div>
                            <div class="col-md-2  mg-b-20">
                                @if (isset($laCompetency->media))
                                    <a target="_blank" href="{{ $imageBaseUrl . $laCompetency->media->path }}">
                                        View Document
                                    </a>
                                @endif
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
    <script src="{{ asset('assets/js/jquery.repeater.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("#missionsForm").parsley();
        });
        var loadFile = function(event) {
            var filePath = event.value;
            var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.jfif|\.pdf)$/i;
            if (!allowedExtensions.exec(filePath)) {
                alert('Please upload file having extensions .jpeg, .jpg .png .pdf only.');
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
