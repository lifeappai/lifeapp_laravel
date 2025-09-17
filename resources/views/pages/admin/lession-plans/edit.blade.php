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
                <h4 class="content-title mb-2">{{ __('Lession Plans') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Lession Plans</li>
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
                    <form method="POST" action="{{ route('admin.lession.plans.update', $laLessionPlan->id) }}"
                        id="missionsForm" enctype="multipart/form-data">
                        @csrf
                        @method('put')
                        <div class="row mb-3 mt-3">
                            <div class="col-md-4  mg-b-20">
                                <h6>Select Language<span class="text-danger">*</span></h6>
                                <select name="la_lession_plan_language_id" class="form-control" required>
                                    <option value="">Select Language</option>
                                    @foreach ($laLessionPlanLanguages as $key => $laLessionPlanLanguage)
                                        <option value="{{ $laLessionPlanLanguage->id }}"
                                            @if ($laLessionPlan->la_lession_plan_language_id == $laLessionPlanLanguage->id) selected @endif>
                                            {{ $laLessionPlanLanguage->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4  mg-b-20">
                                <h6>Select Type<span class="text-danger">*</span></h6>
                                <select name="type" class="form-control" required>
                                    <option value="">Select Type</option>
                                    @foreach (App\Enums\LessionPlanCategoryEnum::Category as $key => $category)
                                        <option value="{{ $category }}"
                                            @if ($laLessionPlan->type == $category) selected @endif>
                                            {{ ucwords(str_replace('_', ' ', strtolower($key))) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12  mg-b-20">
                                <h6>Title<span class="text-danger">*</span></h6>
                                <input type="text" name="title" class="form-control" required
                                    value="{{ $laLessionPlan->title }}">
                            </div>
                            <div class="col-md-10  mg-b-20">
                                <h6>Select Document</h6>
                                <input type="file" class="form-control" id="document" name="document"
                                    autocomplete="off"onchange="loadFile(this)">
                            </div>
                            <div class="col-md-2  mg-b-20">
                                @if (isset($laLessionPlan->media))
                                    <a target="_blank" href="{{ $imageBaseUrl . $laLessionPlan->media->path }}">
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
