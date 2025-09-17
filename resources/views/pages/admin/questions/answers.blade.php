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
                    <div class="row mb-3 mt-3">
                        <div class="col-md-12">
                            <h6 for="">Subject: <span class="ml-2">
                                    {{ $laQuestion->subject ? $laQuestion->subject->default_title : '' }}</span></h6>
                        </div>
                        <div class="col-md-12">
                            <h6 for="">Level: <span class="ml-2"> {{ $laQuestion->level }}</span></h6>
                        </div>
                        <div class="col-md-12">
                            @php $i=1; @endphp
                            <h6 for="">Title (Questions):</h6>
                            @if (count($laQuestion->title) > 0)
                                @foreach ($laQuestion->title as $key => $title)
                                    {{ $i . '. ' }} {{ $key }} = {{ $title }} <br>
                                    <?php $i++; ?>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <hr>
                    <form method="POST" action="{{ route('admin.questions.answers', $laQuestion->id) }}" id="languageForm"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-3 mt-3">
                            <div class="col-md-12  mg-b-20">
                                <h3>Answers<span class="text-danger">*</span></h6>
                            </div>
                            @if (count($answers) > 0)
                                <div class="repeater col-md-12">
                                    <div data-repeater-list="new_option" class="mb-5">
                                        @foreach ($answers as $masterKey => $answer)
                                            <div data-repeater-item class="mb-3" style="border: 1px solid;padding: 15px;">
                                                <div class="new_option_div">
                                                    <div class="row">
                                                        <div class="col-md-11 m-auto">
                                                            <div class="row">
                                                                <div class="col-md-5 mb-3">
                                                                    Language
                                                                </div>
                                                                <div class="col-md-5">
                                                                    Title
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <input type="radio" value="1"
                                                                        name="correct_answer"
                                                                        @if ($laQuestion->answer_option_id == $answer->id) checked @endif><span
                                                                        class="ml-2" style="font-weight: bold"> Correct
                                                                        Answer</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-11 inner-repeater m-auto mt-2"
                                                            style="border: 1px solid;padding: 10px;background: #e6ebef;">
                                                            <div data-repeater-list="option">
                                                                @foreach ($answer->title as $answerKey => $option)
                                                                    <div data-repeater-item class="row mb-3">
                                                                        <div class="col-md-5">
                                                                            <select name="language" id="language1"
                                                                                class="form-control" required>
                                                                                @foreach ($languages as $key => $language)
                                                                                    <option value="{{ $language->slug }}"
                                                                                        selected>
                                                                                        {{ $language->title }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-md-5">
                                                                            <input type="text" class="form-control"
                                                                                id="title" name="title"
                                                                                value="{{ $option }}"
                                                                                placeholder="Enter Title" required=""
                                                                                autocomplete="off">
                                                                        </div>
                                                                        <div class="col-md-2">
                                                                            <input data-repeater-delete="" type="button"
                                                                                class="btn btn-danger waves-effect waves-light"
                                                                                value="Delete">
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            {{-- <div class="text-right mt-2">
                                                                <input data-repeater-create="" type="button"
                                                                    class="btn btn-success mt-3 mt-lg-0"
                                                                    value="Add Translation">
                                                            </div> --}}
                                                        </div>
                                                    </div>
                                                    <div class="text-right mt-2">
                                                        <input data-repeater-delete="" type="button"
                                                            class="btn btn-danger waves-effect waves-light"
                                                            value="Delete This Option">
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="text-right mb-2">
                                        <input data-repeater-create type="button" class="btn btn-success mt-3 mt-lg-0"
                                            value="Add New Option">
                                    </div>
                                </div>
                            @else
                                <div class="repeater col-md-12">
                                    <div data-repeater-list="new_option" class="mb-5">
                                        <div data-repeater-item class="mb-3" style="border: 1px solid;padding: 15px;">
                                            <div class="new_option_div">
                                                <div class="row">
                                                    <div class="col-md-11 m-auto">
                                                        <div class="row">
                                                            <div class="col-md-5 mb-3">
                                                                Language
                                                            </div>
                                                            <div class="col-md-5">
                                                                Title
                                                            </div>
                                                            <div class="col-md-2">
                                                                <input type="radio" value="1"
                                                                    name="correct_answer"><span class="ml-2"
                                                                    style="font-weight: bold"> Correct
                                                                    Answer</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-11 inner-repeater m-auto mt-2"
                                                        style="border: 1px solid;padding: 10px;background: #e6ebef;">
                                                        <div data-repeater-list="option">
                                                            <div data-repeater-item class="row mb-3">
                                                                <div class="col-md-5">
                                                                    <select name="language" id="language1"
                                                                        class="form-control" required>
                                                                        @foreach ($languages as $key => $language)
                                                                            <option value="{{ $language->slug }}">
                                                                                {{ $language->title }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-5">
                                                                    <input type="text" class="form-control"
                                                                        id="title" name="title"
                                                                        placeholder="Enter Title" required=""
                                                                        autocomplete="off">
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <input data-repeater-delete="" type="button"
                                                                        class="btn btn-danger waves-effect waves-light"
                                                                        value="Delete">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        {{-- <div class="text-right mt-2">
                                                            <input data-repeater-create="" type="button"
                                                                class="btn btn-success mt-3 mt-lg-0"
                                                                value="Add Translation">
                                                        </div> --}}
                                                    </div>
                                                </div>
                                                <div class="text-right mt-2">
                                                    <input data-repeater-delete="" type="button"
                                                        class="btn btn-danger waves-effect waves-light"
                                                        value="Delete This Option">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right mb-2">
                                        <input data-repeater-create type="button" class="btn btn-success mt-3 mt-lg-0"
                                            value="Add New Option">
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-12">
                                <button type="button" id="submit" class="btn btn-success">Submit</button>
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
        var $repeater = $('.repeater').repeater({
            repeaters: [{
                selector: '.inner-repeater',
                show: function() {
                    $(this).slideDown()
                },
                hide: function(e) {
                    confirm("Are you sure you want to delete this option value?") && $(this).slideUp(e)
                },
                ready: function(e) {},
                isFirstItemUndeletable: true
            }],
            show: function() {
                var specificSelect = $(this).find('#language1');
                specificSelect.val(specificSelect.find('option:first').val());
                $(this).slideDown();
            },
            hide: function(e) {
                confirm("Are you sure you want to delete this option?") && $(this).slideUp(e)
            },
            ready: function(e) {},
            isFirstItemUndeletable: true

        });
        $(document).on('click', '.new_option_div input[type="radio"]', function() {
            $('.new_option_div input[type="radio"]').each(function() {
                $(this).prop("checked", "");
            });
            $(this).prop("checked", "checked");
        })
        $(document).ready(function() {
            $("#languageForm").parsley();
        });
        document.getElementById("submit").addEventListener("click", function() {
            var type = 0;
            let radioButtons = document.querySelectorAll('input[type=radio]');
            for (let radio of radioButtons) {
                if (radio.checked) {
                    var type = 1;
                }
            }
            if (type == 0) {
                alert("Please Select Correct Answer");
                return false;
            } else {
                $("#submit").attr("type", "submit");
            }
        });
    </script>
@endsection
