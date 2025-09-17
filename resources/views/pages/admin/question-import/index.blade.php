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
                <h4 class="content-title mb-2">{{ __('Import Questions') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Import Questions</li>
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
                    <form method="POST" action="{{ route('admin.import-questions.import') }}" id="languageForm"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-3 mt-3">
                            <div class="col-md-3  mg-b-20">
                                <h6>Select Subject<span class="text-danger">*</span></h6>
                                <select name="la_subject_id" class="form-control" required>
                                    <option value="">Select Subject</option>
                                    @foreach ($subjects as $key => $subject)
                                        <option value="{{ $subject->id }}"
                                            @if (isset($request->la_subject_id) && $request->la_subject_id == $subject->id) selected @endif>{{ $subject->default_title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3  mg-b-20">
                                <h6>Select Level<span class="text-danger">*</span></h6>
                                <select name="la_level_id" class="form-control" required>
                                    <option value="">Select Level</option>
                                    @foreach ($levels as $key => $level)
                                        <option value="{{ $level->id }}"
                                            @if (isset($request->la_level_id) && $request->la_level_id == $level->id) selected @endif>{{ $level->default_title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3  mg-b-20">
                                <h6>Select Topic<span class="text-danger">*</span></h6>
                                <select name="la_topic_id" class="form-control" required>
                                    <option value="">Select Topic</option>
                                    @foreach ($laTopics as $key => $laTopic)
                                        <option value="{{ $laTopic->id }}"
                                            @if (isset($request->la_topic_id) && $request->la_topic_id == $laTopic->id) selected @endif>{{ $laTopic->default_title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3  mg-b-20">
                                <h6>Select Type<span class="text-danger">*</span></h6>
                                <select name="type" class="form-control type" required>
                                    <option value="{{ App\Enums\GameType::QUIZ }}"
                                        @if (isset($request->type) && $request->type == App\Enums\GameType::QUIZ) selected @endif>Quiz</option>
                                    <option value="{{ App\Enums\GameType::RIDDLE }}"
                                        @if (isset($request->type) && $request->type == App\Enums\GameType::RIDDLE) selected @endif>Riddle</option>
                                    <option value="{{ App\Enums\GameType::PUZZLE }}"
                                        @if (isset($request->type) && $request->type == App\Enums\GameType::PUZZLE) selected @endif>Puzzle</option>
                                </select>
                            </div>

                            <div class="col-md-6  mg-b-20">
                                <h6>Import excel sheet<span class="text-danger">*</span></h6>
                                <input type="file" class="form-control" name="question_excel_sheet"
                                    id="question_excel_sheet">
                                <label>Download Sample File From Here: <a href="{{ asset('assets/excel/QAImport.csv') }}"
                                        ><b>QA Options File</b></a></label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-success">Submit</button>
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
    <script src="{{ asset('assets/js/jquery.repeater.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $(".repeater").repeater({
                show: function() {
                    $(this).slideDown()
                },
                hide: function(e) {
                    confirm("Are you sure you want to delete this element?") && $(this).slideUp(e)
                },
                ready: function(e) {},
                isFirstItemUndeletable: true
            });
            $("#languageForm").parsley();
        });
    </script>
@endsection
