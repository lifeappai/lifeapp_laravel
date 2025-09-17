@extends('layouts.admin')
@section('css')
    <style>
        .error {
            color: red;
            margin-top: 5px;
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
                <h4 class="content-title mb-2">{{ __('Questions') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Questions</li>
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
                    <form method="POST" action="{{ route('admin.questions.update', $laQuestion->id) }}" id="languageForm"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row mb-3 mt-3">
                            <div class="col-md-3   mg-b-20">
                                <h6>Select Subject<span class="text-danger">*</span></h6>
                                <select name="la_subject_id" class="form-control" required>
                                    <option value="">Select Subject</option>
                                    @foreach ($subjects as $key => $subject)
                                        <option value="{{ $subject->id }}"
                                            @if ($laQuestion->la_subject_id == $subject->id) selected @endif>{{ $subject->default_title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3   mg-b-20">
                                <h6>Select Level<span class="text-danger">*</span></h6>
                                <select name="la_level_id" class="form-control" required>
                                    <option value="">Select Level</option>
                                    @foreach ($levels as $key => $level)
                                        <option value="{{ $level->id }}"
                                            @if ($laQuestion->la_level_id == $level->id) selected @endif>{{ $level->default_title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3   mg-b-20">
                                <h6>Select Topic<span class="text-danger">*</span></h6>
                                <select name="la_topic_id" class="form-control" required>
                                    <option value="">Select Topic</option>
                                    @foreach ($laTopics as $key => $laTopic)
                                        <option value="{{ $laTopic->id }}"
                                            @if ($laQuestion->la_topic_id == $laTopic->id) selected @endif>{{ $laTopic->default_title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3  mg-b-20">
                                <h6>Select Type<span class="text-danger">*</span></h6>
                                <select name="type" class="form-control type" required>
                                    <option value="{{ App\Enums\GameType::QUIZ }}"
                                        @if ($laQuestion->type == App\Enums\GameType::QUIZ) selected @endif>Quiz</option>
                                    <option value="{{ App\Enums\GameType::RIDDLE }}"
                                        @if ($laQuestion->type == App\Enums\GameType::RIDDLE) selected @endif>Riddle</option>
                                    <option value="{{ App\Enums\GameType::PUZZLE }}"
                                        @if ($laQuestion->type == App\Enums\GameType::PUZZLE) selected @endif>Puzzle</option>
                                </select>
                            </div>
                            <div
                                class="col-md-3  mg-b-20  question_type_class @if ($laQuestion->type != App\Enums\GameType::PUZZLE) d-none @endif">
                                <h6>Select Question Type</h6>
                                <select name="question_type" class="form-control question_type">
                                    <option value="{{ App\Enums\GameType::QUESTION_TYPE['TEXT'] }}"
                                        @if ($laQuestion->question_type == App\Enums\GameType::QUESTION_TYPE['TEXT']) selected @endif>Text</option>
                                    <option value="{{ App\Enums\GameType::QUESTION_TYPE['IMAGE'] }}"
                                        @if ($laQuestion->question_type == App\Enums\GameType::QUESTION_TYPE['IMAGE']) selected @endif>Image</option>
                                </select>
                            </div>
                            <div class="col-md-12  mg-b-20">
                                <h6>Add Question Title<span class="text-danger">*</span></h6>
                            </div>
                            <table class="table table-bordered repeater">
                                <thead class="align-middle">
                                    <tr>
                                        <th style="width: 40%">Language</th>
                                        <th style="width: 40%">Title</th>
                                        <th style="width: 20%">
                                            <input data-repeater-create="" type="button"
                                                class="btn btn-success mt-3 mt-lg-0" value="Add">
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="align-middle" data-repeater-list="question_translations">
                                    @foreach ($laQuestion->title as $key => $title)
                                        <tr data-repeater-item="">
                                            <td>
                                                <select name="language" id="language1" class="form-control" required>
                                                    @foreach ($languages as $language)
                                                        <option value="{{ $language->slug }}"
                                                            @if ($key == $language->slug) selected @endif>
                                                            {{ $language->title }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="title">
                                                @if ($laQuestion->question_type == App\Enums\GameType::QUESTION_TYPE['IMAGE'])
                                                    <input type="file" class="form-control" id="title" name="title"
                                                        autocomplete="off">
                                                    @php
                                                        $media = $laQuestion->getMedia($title);
                                                    @endphp
                                                    @if ($media)
                                                        <a class="image-popup-no-margins"
                                                            href="{{ asset('storage/' . $media->path) }}">
                                                            <img alt=""
                                                                src="{{ asset('storage/' . $media->path) }}"
                                                                class="custom-thumbnail">
                                                        </a>
                                                    @endif
                                                    <input type="hidden" class="form-control" id="media_id"
                                                        name="media_id" value="{{ $title }}">
                                                @else
                                                    <input type="text" class="form-control" id="title" name="title"
                                                        placeholder="Enter Title" required=""
                                                        value="{{ $title }}" autocomplete="off">
                                                @endif
                                            </td>
                                            <td>
                                                <input data-repeater-delete="" type="button"
                                                    class="btn btn-danger waves-effect waves-light" value="Delete">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-success">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
                <br>
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
        $(".type").change(function() {
            $(".question_type").val(1);
            $(".title").each(function(index) {
                $(this).html(
                    '<input type="text" class="form-control" id="title"  name="question_translations[' +
                    index + '][title]" placeholder="Enter Title" required="" autocomplete="off">'
                );
            });
            $(".question_type_class").addClass('d-none');
            if ($(this).val() == 4) {
                $(".question_type_class").removeClass('d-none');
            }
        });
        $(".question_type").change(function() {
            $(".title").each(function(index) {
                $(this).html(
                    '<input type="text" class="form-control" id="title"  name="question_translations[' +
                    index + '][title]" placeholder="Enter Title" required="" autocomplete="off">'
                );
                if ($(".question_type").val() == "2") {
                    $(this).html(
                        '<input type="file" class="form-control" id="title"  name="question_translations[' +
                        index +
                        '][title]" placeholder="Enter Title" required="" autocomplete="off">'
                    );
                }
            });
        });
        $(document).ready(function() {
            $(".repeater").repeater({
                show: function() {
                    console.log($(this));
                    var repeaterItem = $(this);
                    var titleField = repeaterItem.find('.title');
                    var titleFieldName = titleField.find('input').attr('name');
                    var specificSelect = $(this).find('#language1');
                    specificSelect.val(specificSelect.find('option:first').val());
                    if ($(".question_type").val() == "2") {
                        titleField.html(
                            '<input type="file" class="form-control" id="title" name="' +
                            titleFieldName +
                            '" placeholder="Enter Title" required="" autocomplete="off">'
                        );
                    } else {
                        titleField.html(
                            '<input type="text" class="form-control" id="title" name="' +
                            titleFieldName +
                            '" placeholder="Enter Title" required="" autocomplete="off">'
                        );
                    }
                    repeaterItem.slideDown();
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
