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
                <h4 class="content-title mb-2">{{ __('Topics') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Topics</li>
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
                    <form method="POST" action="{{ route('admin.topics.store') }}" id="topiceForm" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-3  mg-b-20">
                                <h6>Select Allow For<span class="text-danger">*</span></h6>
                                <select name="allow_for" class="form-control" required>
                                    @foreach (App\Enums\GameType::ALLOW_FOR as $key => $allowFor)
                                        <option value="{{ $allowFor }}">
                                            {{ ucwords(str_replace('_', ' ', strtolower($key))) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3  mg-b-20">
                                <h6>Select Type<span class="text-danger">*</span></h6>
                                <select name="type" class="form-control type" required>
                                    <option value="{{ App\Enums\GameType::QUIZ }}" @if ($request->type == App\Enums\GameType::QUIZ) selected @endif>Quiz</option>
                                    <option value="{{ App\Enums\GameType::RIDDLE }}" @if ($request->type == App\Enums\GameType::RIDDLE) selected @endif>Riddle</option>
                                    <option value="{{ App\Enums\GameType::PUZZLE }}" @if ($request->type == App\Enums\GameType::PUZZLE) selected @endif>Puzzle</option>
                                </select>
                            </div>
                            <div class="col-md-3  mg-b-20">
                                <h6>Select Subject<span class="text-danger">*</span></h6>
                                <select name="la_subject_id" class="form-control type" required>
                                    <option value="">Select Subject</option>
                                    @foreach ($laSubjects as $laSubject)
                                        <option value="{{ $laSubject->id }}" @if (isset($request->la_subject_id) && $request->la_subject_id == $laSubject->id) selected @endif>{{ $laSubject->default_title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3  mg-b-20">
                                <h6>Select Level<span class="text-danger">*</span></h6>
                                <select name="la_level_id" class="form-control" required>
                                    <option value="">Select Level</option>
                                    @foreach ($laLevels as $key => $laLevel)
                                        <option value="{{ $laLevel->id }}" @if (isset($request->la_level_id) && $request->la_level_id == $laLevel->id) selected @endif>{{ $laLevel->default_title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <table class="table table-bordered repeater">
                                <thead class="align-middle">
                                    <tr>
                                        <th style="width: 25%">Language</th>
                                        <th style="width: 25%">Title</th>
                                        <th style="width: 25%">Image</th>
                                        <th style="width: 25%">
                                            <input data-repeater-create="" type="button"
                                                class="btn btn-success mt-3 mt-lg-0" value="Add">
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="align-middle" data-repeater-list="topic_translation">
                                    <tr data-repeater-item="">
                                        <td>
                                            <select name="language" id="language1" class="form-control" required>
                                                @foreach ($languages as $key => $language)
                                                    <option value="{{ $language->slug }}">{{ $language->title }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" id="title" name="title"
                                                placeholder="Enter Title" required="" autocomplete="off">
                                        </td>
                                        <td>
                                            <input type="file" class="form-control" id="image" name="image"
                                                required="" autocomplete="off" onchange="loadFile(this)">
                                        </td>
                                        <td>
                                            <input data-repeater-delete="" type="button"
                                                class="btn btn-danger waves-effect waves-light" value="Delete">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
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
    <script src="{{ asset('assets/js/jquery.repeater.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $(".repeater").repeater({
                show: function() {
                    var specificSelect = $(this).find('#language1');
                    specificSelect.val(specificSelect.find('option:first').val());
                    $(this).slideDown()
                },
                hide: function(e) {
                    confirm("Are you sure you want to delete this element?") && $(this).slideUp(e)
                },
                ready: function(e) {},
                isFirstItemUndeletable: true
            });
            $("#topiceForm").parsley();
        });

        var loadFile = function(event) {
            var filePath = event.value;
            var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.jfif)$/i;
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
