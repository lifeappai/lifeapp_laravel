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
                    <form method="POST" action="{{ route('admin.topics.update', $laTopic->id) }}" id="topicForm"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row mb-3">
                                <div class="col-md-3  mg-b-20">
                                    <h6>Select Allow For<span class="text-danger">*</span></h6>
                                    <select name="allow_for" class="form-control" required>
                                        @foreach (App\Enums\GameType::ALLOW_FOR as $key => $allowFor)
                                            <option value="{{ $allowFor }}"
                                                @if ($laTopic->allow_for == $allowFor) selected @endif>
                                                {{ ucwords(str_replace('_', ' ', strtolower($key))) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            <div class="col-md-3  mg-b-20">
                                <h6>Select Type<span class="text-danger">*</span></h6>
                                <select name="type" class="form-control type" required>
                                    <option value="{{ App\Enums\GameType::QUIZ }}" @if (isset($laTopic->type) && $laTopic->type == App\Enums\GameType::QUIZ) selected @endif>Quiz</option>
                                    <option value="{{ App\Enums\GameType::RIDDLE }}" @if (isset($laTopic->type) && $laTopic->type == App\Enums\GameType::RIDDLE) selected @endif>Riddle</option>
                                    <option value="{{ App\Enums\GameType::PUZZLE }}" @if (isset($laTopic->type) && $laTopic->type == App\Enums\GameType::PUZZLE) selected @endif>Puzzle</option>
                                </select>
                            </div>
                            <div class="col-md-3  mg-b-20">
                                <h6>Select Subject<span class="text-danger">*</span></h6>
                                <select name="la_subject_id" class="form-control type" required>
                                    <option value="">Select Subject</option>
                                    @foreach ($laSubjects as $laSubject)
                                        <option value="{{ $laSubject->id }}" @if (isset($laTopic->la_subject_id) && $laTopic->la_subject_id == $laSubject->id) selected @endif>{{ $laSubject->default_title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3  mg-b-20">
                                <h6>Select Level<span class="text-danger">*</span></h6>
                                <select name="la_level_id" class="form-control" required>
                                    <option value="">Select Level</option>
                                    @foreach ($laLevels as $key => $laLevel)
                                        <option value="{{ $laLevel->id }}" @if (isset($laTopic->la_level_id) && $laTopic->la_level_id == $laLevel->id) selected @endif>{{ $laLevel->default_title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                                <table class="table table-bordered repeater">
                                    <thead class="align-middle">
                                        <tr>
                                            <th>Language</th>
                                            <th>Title</th>
                                            <th>Image</th>
                                            <th>
                                                <input data-repeater-create="" type="button"
                                                    class="btn btn-success mt-3 mt-lg-0" value="Add">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="align-middle" data-repeater-list="topic_translation">
                                        @foreach ($laTopic->title as $keyData => $title)
                                            <tr data-repeater-item="">
                                                <td>
                                                    <select {{ $keyData }} name="language" id="language1"
                                                        class="form-control" required>
                                                        @foreach ($languages as $key => $language)
                                                            <option value="{{ $language->slug }}"
                                                                @if ($keyData == $language->slug) selected @endif>
                                                                {{ $language->title }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" name="title" class="form-control" required
                                                        @if (isset($laTopic->title[$keyData])) value="{{ $laTopic->title[$keyData] }}" @endif>
                                                </td>
                                                <td>
                                                    <input type="file" class="form-control" id="image" name="image"
                                                        autocomplete="off" onchange="loadFile(this)">
                                                </td>
                                                <td>
                                                    @if (isset($laTopic->image[$keyData]))
                                                        @php $mediaPath = $laTopic->getMediaPath($laTopic->image[$keyData]); @endphp
                                                        @if ($mediaPath)
                                                            <input type="hidden" class="form-control" id="media_id"
                                                                name="media_id" value="{{ $laTopic->image[$keyData] }}">
                                                            <a target="_blank"
                                                                href="{{ $imageBaseUrl . $mediaPath->path }}">
                                                                View Image
                                                            </a>
                                                            <br>
                                                        @endif
                                                    @endif
                                                    <input data-repeater-delete="" type="button"
                                                        class="btn btn-danger btn-sm waves-effect waves-light"
                                                        value="Delete">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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
    <script src="{{ asset('assets/js/jquery.repeater.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $(".repeater").repeater({
                show: function() {
                    var specificSelect = $(this).find('#language1');
                    specificSelect.val(specificSelect.find('option:first').val());
                    $(this).find('a').css("display", "none");
                    $(this).find('input[type="file"]').attr("required", true);
                    $(this).slideDown()
                },
                hide: function(e) {
                    confirm("Are you sure you want to delete this element?") && $(this).slideUp(e)
                },
                ready: function(e) {},
                isFirstItemUndeletable: true
            });
            $("#topicForm").parsley();
        });
    </script>
@endsection
